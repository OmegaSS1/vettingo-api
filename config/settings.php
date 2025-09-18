<?php

declare(strict_types=1);

use App\Settings\Settings;
use App\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => false, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                "memory_limit" => '512M',
                "max_execution_time" => "500",
                "mb_internal_encoding" => "UTF-8",
                "locale" => [
                    "category" => LC_ALL,
                    "locales" => "pt_BR", "pt_BR.utf-8", "portuguese"
                ],
                "date_default_timezone_set" => "America/Sao_Paulo",
                "html" => [
                    "path"  => __DIR__."/../public/src",
                    "cache" => __DIR__."/../public/cache"
                ]
            ]);
        }
    ]);
};
