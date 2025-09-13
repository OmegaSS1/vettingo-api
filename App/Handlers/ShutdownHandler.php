<?php

declare(strict_types=1);
namespace App\Handlers;
use App\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpInternalServerErrorException;
class ShutdownHandler
{
    private Request $request;
    private HttpErrorHandler $errorHandler;
    private bool $displayErrorDetails;
    private LoggerInterface $logger;

    public function __construct(
        Request $request,
        HttpErrorHandler $errorHandler,
        bool $displayErrorDetails,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logger = $logger;
    }
    public function __invoke()
    {
        $error = error_get_last();
        $enableError = false;

        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $this->logger->error($errorMessage, []); 
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: {$errorMessage}. ";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                    case E_USER_WARNING:
                        $message = "WARNING: {$errorMessage}";
                        break;
                    case E_USER_NOTICE:
                        $message = "NOTICE: {$errorMessage}";
                        break;
                    default:
                        $message = "ERROR: {$errorMessage}";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                }
            }

            $this->logger->error($message, []); 
            $exception = new HttpInternalServerErrorException($this->request, $message);
            if($enableError){
                $response = $this->errorHandler->__invoke(
                    $this->request,
                    $exception,
                    $this->displayErrorDetails,
                    false,
                    false,
                );

                $responseEmitter = new ResponseEmitter();
                $responseEmitter->emit($response);
            }
        }
    }
}

