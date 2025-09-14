<?php

namespace App\Traits;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as newResponse;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Datetime;
use Exception;
use App\Model\Database as DB;

trait Helper {
	public $loggerHelper = null;

	public function post(): array
	{
		$form = json_decode(file_get_contents('php://input'), true);
		if (empty($form)) 
			$form = $_POST;

		if (isset($form['form']))
			$form = $form['form'];

		return $form;
	}

	public function blackList($cpf): void{
		$db = new DB($this->loggerHelper);

		$res = $db->select('*', 'tb_black_list', "loginn = '$cpf'");
		if(empty($res)){
			$db->insert('tb_black_list', ['loginn' => $cpf, 'ip' => $_SERVER['REMOTE_ADDR'], 'tentativa' => 1]);
			exit;
		}

		#Consulta a black list
		$data = $db->select('*', 'tb_black_list', "loginn = '$cpf'", "tentativa >= 10");
		if(!empty($data)) throw new Exception('Usuario bloqueado por excesso de tentativa. Solicite o debloqueio com a sua gestão/coordenadoria.', 400);
		
		$db->update('tb_black_list', ['tentativa' => ++$res[0]['tentativa']], "loginn = '$cpf'");
	}

	public function releaseBlackList($cpf): void{
		$db = new DB($this->loggerHelper);
		$db->update('tb_black_list', ['tentativa' => 0], "loginn = '$cpf'");
	}

	/**
	 * Retorna o ultimo erro da aplicação.
	 *
	 * @param Request $request Request da requisição HTTP.
	 * @param Response $response Reponse da requisição HTTP.
	 * @param mixed $content Conteudo como resposta da requisição.
	 */
	public function error(string $uri, Exception $e): Response
	{
		$response = new newResponse();
		$statusCode = $e->getCode();
		$message = $e->getMessage();

		switch($statusCode){
			case 400:
				$error = "Bad Request";
				break;
			case 401:
				$error = "Unauthorized";
				break;
			case 402:
				$error = "Payment Required";
				break;
			case 403:
				$error = "Forbidden";
				break;
			case 404:
				$error = "Not Found";
				break;
			case 405:
				$error = "Method Not Allowed";
				break;
			default:
				$statusCode = 500;
				$message = "Erro inesperado durante a execução";
				$error = "Internal Server Error";
		}
		
		$payload = [
            "statusCode" => $statusCode,
            "message" => $message,
            "error" => $error,
            "timestamp" => date('Y-m-d H:i:s'),
            "path" => $uri,
        ];

		$response->getBody()->write(json_encode($payload));
		return $response->withStatus($statusCode);
	}

	/**
	 * Summary of success
	 * @param string $message
	 * @param int $code
	 * @return array{code: int, message: string}
	 */
	public function success(string $message, int $code = 200): array{
		return array("message" => $message, "code" => $code);
	}

	public function saveLogger(Request $request, array $loggerInfo = []): void{
        $USER = $this->request->getAttribute("USER") ?? (object) ["sub" => "Usuário não logado"];
		
        $loggerInfo["User"]   = $USER->sub; 
        $loggerInfo["Ip"]     = IP;
        $loggerInfo["Method"] = $request->getMethod(); 
        $loggerInfo["Route"]  = $request->getUri()->getPath();
		
		if($request->getMethod() !== 'GET'){
            $this->loggerHelper->info(json_encode($loggerInfo, JSON_UNESCAPED_UNICODE), $this->post() ?? $request->getParsedBody() ?? $request->getQueryParams());
			//(new DB($this->loggerHelper))->insert('tb_log', [
			//	"id_usuario" => $USER->sub,
			//	"rota"       => $request->getUri()->getPath(),
			//	"request"    => json_encode($this->post() ?? (string) $request->getParsedBody() ?? (string) $request->getQueryParams(), JSON_UNESCAPED_UNICODE),
			//	"response"   => json_encode($loggerInfo['Response'], JSON_UNESCAPED_UNICODE)
			//]);
		}
	}

	/**
	 * Fornmata a string substituindo caracteres especiais
	 */
	public function format_string(string $string):string {
		return preg_replace(array("//", "/á|à|ã|â|ä/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/"), explode(" ", " a A e E i I o O u U n N c"), $string);
	}

	public function validateStrongPassword(string $password, int $minLength, int $statusCode = 400){
		$forcePassword = ['letras maiúsculas' => '[A-Z]', 'letras minúsculas' => '[a-z]', 'números' => '[0-9]', 'caracteres especiais' => '[-!$%#^&*()@_+{}~=`\[\]:;<>?.,|\'\"]'];
		foreach ($forcePassword as $exception => $force) {
			preg_match("/$force/", $password, $matches);
			if (empty($matches)) throw new Exception("A senha deve conter $exception!", $statusCode);
		}
		if (strlen($password) < $minLength) throw new Exception("A senha deve conter um mínimo de $minLength caracteres!", $statusCode);
	}

	public function validateCPF(string $cpf){
		$cpf = preg_replace('/\D/', '', $cpf);
		
		if (empty($cpf) or strlen($cpf) < 11) 
			throw new Exception('CPF inválido!', 400);
		else if (preg_match('/(\d)\1{10}/', $cpf)) 
			throw new Exception('CPF inválido!', 400);

		$validacao = substr($cpf, 0, 9);

		for ($i = 0; $i < 2; $i++) {
			$calculo = strlen($validacao) + 1;
			$soma = 0;

			for ($c = 0; $c < strlen($validacao); $c++) {
				$soma += $validacao[$c] * $calculo;
				$calculo--;
			}
			$validacao .= $soma % 11 > 1 ? 11 - ($soma % 11) : 0;
		}

		if ($validacao != $cpf) throw new Exception('CPF inválido!', 400);
	}

		/**
	 * Compara duas datas, e retorna a diferença de tempo entre elas.
	 *
	 * @param string $min Data mais antiga.
	 * @param string $max Data mais recente.
	 * @param string $type [optional] Caso nenhum valor seja atribuído, retorna todas as diferenças de tempo entre as datas.
	 * @return mixed
	 *
	 * @annotation
	 * y-m-d Retorna ano, mês e dia.
	 * y-m   Retorna ano e mês.
	 * y     Retorna ano.
	 * m     Retorna mẽs.
	 * d     Retorna dia.
	 * h-i-s Retorna hora, minuto e segundo.
	 * h-i   Retorna hora e minuto.
	 * h     Retorna hora.
	 * i 	   Retorna minuto.
	 * s 	   Retorna segunto.
	 * days  Retorna a diferença total em dias.
	 */
	public function diffBetweenDatetimes(string $min, string $max, string $type = '')
	{
		$min = new Datetime($min);
		$max = new Datetime($max);
		$diff = $min->diff($max);

		if (!empty($min) && !empty($max)) {
			switch ($type) {
				case 'y-m-d':
					return $diff->y . '-' . $diff->m . '-' . $diff->d;
				case 'y-m':
					return $diff->y . '-' . $diff->m;
				case 'y':
					return $diff->y;
				case 'm':
					return $diff->m;
				case 'd':
					return $diff->d;
				case 'h-i-s':
					return $diff->h . ':' . $diff->i . ':' . $diff->s;
				case 'h-i':
					return $diff->h . ':' . $diff->i;
				case 'h':
					return $diff->h;
				case 'i':
					return $diff->i;
				case 's':
					return $diff->s;
				case 'days':
					return $diff->days;
				default:
					return $diff;
			}
		}
	}

		/**
	 * Verifica se a chave existe, vazio ou < 0
	 * @param array $form Dados do client
	 * @param array $keys Chaves do array
	 * @param array $message Nome do Campo para Exception. 
	 * Note que caso a chave seja um array, a mensagem precisa estar dentro de um array
	 * @return void
	 * @throws Exception
	 */
	
	 public function validKeysForm (array $form, array $keys, array $message, bool $returnWithKey = false, int $statusCode = 400): void
	{
		foreach ($keys as $k => $v) {
			if (!isset($form[$v]) || $form[$v] === "" || $form[$v] === null) {
				$message = "É obrigatório informar o campo " . strtoupper($message[$k]);
				$message = !$returnWithKey ? $message : json_encode(["message" => $message, "name" => $v] , JSON_UNESCAPED_UNICODE);
				throw new Exception($message, $statusCode);
			} 
			else if (is_float($form[$v]) or is_int($form[$v])) {
				if ($form[$v] < 0) {
					$message = "O valor do campo " . strtoupper($message[$k]) . " não pode ser menor que 0";
					$message = !$returnWithKey ? $message : json_encode(["message" => $message, "name" => $v] , JSON_UNESCAPED_UNICODE);
					throw new Exception($message, $statusCode);
				}
			}
			else if(is_array($form[$v])){
				$this->validKeysForm($form[$v], array_keys($form[$v]), array_values($message[$k]));
			}
		}
	}

	public function toArray(&$data){
    	// Se for objeto, transforma em array
		if (is_object($data)) {
			$data = json_decode(json_encode($data),true);
		}
		
		if (is_array($data)) {
			$data = array_filter($data, function($value) {
				return !in_array($value, ['', [], null], true);
			});
		}

		// Se não for array, retorna valor encapsulado em array
		if (!is_array($data)) {
			return [$data];
		}

		// Percorre recursivamente
		foreach ($data as $key => $value) {
			if (is_object($value) || is_array($value)) {
				$data[$key] = $this->toArray($value);
			}
		}

		return $data;
	}

	public function decodeBase64(string $img, string $filename, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']){
		if(preg_match('/^data:image\/(\w+);base64,/', $img, $type)){
			$img = substr($img, strpos($img, ',') +1);
			$ext = strtolower($type[1]);

			if(!in_array($ext, $allowedExtensions)){
				throw new Exception('Formato de imagem inválido.', 400);
			}

			$data = base64_decode($img);
			if($data === false){
				throw new Exception('Formato Base64 inválido.',400);
			}
			$length = strlen($data) / 1024 / 1024;
			$length = number_format($length, 2);
			
			$filename = pathinfo($filename, PATHINFO_BASENAME) . "." . $ext;

			return [$filename, $data, $length];
		}
		else {
    		throw new Exception('String base64 inválida.');
		}
	}

	public function validateTime(string $time){
		return preg_match("/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $time);
	}

	public function convertDate(string $date){
		$newDate = NULL;
		$regexMap = [
			    'd-m-Y' => '/^\d{2}-\d{2}-\d{4}$/',
				'dmY'   => '/^\d{8}$/',
				'd/m/Y' => '/^\d{2}\/\d{2}\/\d{4}$/',
				'Y/m/d' => '/^\d{4}\/\d{2}\/\d{2}$/',
			    'Y-m-d' => '/^\d{4}-\d{2}-\d{2}$/',
		];

		foreach($regexMap as $key => $value){
			if(preg_match($value, $date)){
				$newDate = DateTime::createFromFormat($key, $date);
				return $newDate->format('Y-m-d');
			}
		}
	}
}