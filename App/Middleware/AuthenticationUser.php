<?php

namespace App\Middleware;

use App\Repository\User\IUserRepository;
use App\Services\TokenJWT;
use App\Traits\Helper;
use PDOException;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Psr\Log\LoggerInterface;

class AuthenticationUser implements Middleware {

    use Helper;
    private TokenJWT $tokenJWT;
    private IUserRepository $iUserRepository;
    private LoggerInterface $loggerInterface;

  public function __construct(
    TokenJWT $tokenJWT, 
    LoggerInterface $loggerInterface,
    IUserRepository $iUserRepository
  ){
    $this->tokenJWT = $tokenJWT;
    $this->loggerInterface = $loggerInterface;
    $this->iUserRepository = $iUserRepository;
  }
  public function process(Request $request, RequestHandler $handler): Response{
    $uri = $request->getUri()->getPath();  
    try {

      $bearer       = $request->getHeaderLine('Authorization') ?? throw new Exception("Authorization header missing", 401);    
      $bearerSplit  = explode(' ', $bearer);
      $token        = $bearerSplit[1] ?? throw new Exception("Authorization header missing", 401);
      
      try{
        $decode_token = JWT::decode($token, new Key(ENV['KEY'], 'HS256'));
      } catch(Exception $e){
        $message = "O usuário não foi autenticado, faça login novamente!";
        $this->loggerInterface->warning("(MIDDLEWARE) $message", [$decode_token->data ?? $token ?? []]);
        return $this->error($uri, new Exception($message, 401));
      }

      $logInfo      = [
        "User"     => $decode_token->sub ?? "Usuário não logado",
        "Ip"       => IP,
        "Method"   => $request->getMethod(),
        "Route"    => $request->getUri()->getPath(),
      ];

      $user = $this->iUserRepository->findById($decode_token->sub);

      if($decode_token->iss != IP) {
        throw new Exception("(MIDDLEWARE) Falha na verificação de origem do token", 401);
      } 
      else if(!$user) {
        throw new Exception("(MIDDLEWARE) Usuário não localizado pelo id {$decode_token->sub}", 401);
      }
      else if(!$user->getIsActive()){
        throw new Exception("(MIDDLEWARE) Não é permitido recuperar nem editar os dados de um usuário inativo", 401);
      }

      $request = $request->withAttribute('USER', $decode_token)->withAttribute('ROLE', $user->getRole());
      return $handler->handle($request);
      
    } catch(Exception | PDOException $e){
      $logInfo['Response'] = array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine());
      $this->loggerInterface->warning(json_encode($logInfo, JSON_UNESCAPED_UNICODE), [$decode_token->data ?? $token ?? []]);
      
      return $this->error($uri, new Exception($e->getMessage(), $e->getCode()));
    }
  }
}
