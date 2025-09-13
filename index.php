<?php

use App\Handlers\HttpErrorHandler;
use App\Handlers\ShutdownHandler;
use App\ResponseEmitter\ResponseEmitter;
use App\Settings\SettingsInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Factory\ServerRequestCreatorFactory;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/slimConfiguration.php';
require_once __DIR__ . '/config/constants.php';

$routes = require_once __DIR__ . '/config/router.php';

$containerBuilder = new ContainerBuilder();

$repositories = require_once __DIR__ . '/config/repositories.php';
$repositories($containerBuilder);

$settings = require_once __DIR__ . '/config/settings.php';
$settings($containerBuilder);

$dependencies = require_once __DIR__ . '/config/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

$middleware = require_once __DIR__ . '/config/middleware.php';
$middleware($app);

$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

/** @var \Psr\Log\LoggerInterface $loggerInterface */
$loggerInterface = $container->get(LoggerInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

ini_set('memory_limit', $settings->get('memory_limit'));
ini_set('max_execution_time', $settings->get('max_execution_time'));
ini_set('default_charset', 'UTF-8');
ini_set('display_errors', $settings->get('displayErrorDetails'));
mb_internal_encoding($settings->get('mb_internal_encoding'));
setlocale($settings->get('locale')['category'], $settings->get('locale')['locales']);
date_default_timezone_set($settings->get('date_default_timezone_set'));

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails, $loggerInterface);
register_shutdown_function($shutdownHandler);

$app->setBasePath(ENV['PATH_APPLICATION']);
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);

?>
