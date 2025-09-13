<?php

declare(strict_types= 1);
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

use App\Traits\Helper;
use Exception;

class AuthenticationUserRole implements Middleware {
  use Helper;

  private LoggerInterface $loggerInterface;
  private string $role;

  public function __construct(string $role, LoggerInterface $loggerInterface,){
    $this->role = $role;
    $this->loggerInterface = $loggerInterface;
  }

  public function process(Request $request, RequestHandler $handler): Response{
    $uri = $request->getUri()->getPath();  
    $role = $request->getAttribute("ROLE");

    try {
      if($this->role != $role){
        throw new Exception('O usuário não tem permissão para acessar esta funcionalidade!', 401);
      }
      return $handler->handle($request);
    }
    catch(Exception $e){
      $logInfo['Response'] = array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine());
      $this->loggerInterface->warning(json_encode($logInfo, JSON_UNESCAPED_UNICODE), [$decode_token->data ?? $token ?? []]);

      return $this->error($uri, new Exception($e->getMessage(), $e->getCode()));
    }
  }
  
}