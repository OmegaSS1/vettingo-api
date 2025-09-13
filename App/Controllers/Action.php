<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\Database\IDatabaseRepository;
use Psr\Log\LoggerInterface;
use App\Traits\{Helper, MessageException};
use Exception;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    use Helper, MessageException;
    protected LoggerInterface $loggerInterface;
    protected IDatabaseRepository $iDatabaseRepository;
    protected Request $request;
    protected Response $response;
    protected array $args;
    protected $USER;
    protected array $logInfo;
    
    public function __construct(LoggerInterface $loggerInterface, IDatabaseRepository $iDatabaseRepository)
    {
        $this->loggerInterface = $loggerInterface;
        $this->loggerHelper = $loggerInterface;
        $this->iDatabaseRepository = $iDatabaseRepository;
    }

    /**
     * @throws \Slim\Exception\HttpNotFoundException
     * @throws \Slim\Exception\HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request  = $request;
        $this->response = $response;
        $this->args     = $args;
        $this->USER     = $this->request->getAttribute("USER") ?? (object) ["sub" => "Usuário não logado"];
        $this->logInfo  = [
            "User"     => $this->USER->data->id, 
            "Ip"       => IP, 
            "Method"   => $this->request->getMethod(), 
            "Route"    => $this->request->getUri()->getPath(),

        ];
        try {
            return $this->action();
        } catch (Exception $e) {
            $this->logInfo["Response"] = $e->getMessage();
            $this->loggerInterface->error(json_encode($this->logInfo, JSON_UNESCAPED_UNICODE), $this->request->getParsedBody() ?? $this->args ?? $this->request->getQueryParams());
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    abstract protected function action(): Response;

    protected function getArg(string $name)
    {
        if (!isset($this->args[$name])) 
            return ""; 
        return $this->args[$name];
    }

    /**
     * @return array|object
     */
    protected function getFormData()
    {
        return $this->request->getParsedBody();
    }

    protected function respondWithError($e): Response
    {   
        $logInfo['Response'] = array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine());
        $this->saveLogger($this->request, $logInfo);
        
		$this->response->getBody()->write(json_encode(array('message' => $e->getMessage(), 'statusCode' => $e->getCode()), JSON_UNESCAPED_UNICODE));
        $this->iDatabaseRepository->destruction();

        return $this->response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode());
    }

    protected function respondWithData(array $data, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);
        
        if($this->request->getMethod() !== 'GET'){
            $this->logInfo['Response'] = $statusCode;
            $this->loggerInterface->info(json_encode($this->logInfo, JSON_UNESCAPED_UNICODE), $this->request->getParsedBody() ?? $this->args ?? $this->request->getQueryParams());
            $this->saveLogger($this->request, $this->logInfo);
        }

        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);
        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }
}
