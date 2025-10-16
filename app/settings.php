<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => !(getenv('ENVIRONMENT') === 'production'),
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' =>  __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'twigger' => [
                    'path'  => __DIR__ . '/../storage/templates',
                    'settings' => getenv('ENVIRONMENT') === 'development' ? [] : [
                        'cache' => __DIR__ . '/../var/cache/twig',
                    ]
                ],
                'database' => [
                    'default' => 'mysql',

                    'mysql' => [
                        'driver'    => 'mysql',
                        'name'      => getenv('DB_DATABASE_NAME'),
                        'user'      => getenv('DB_USERNAME'),
                        'password'  => getenv('DB_PASSWORD') ?: "",
                        'host'      => getenv('DB_HOST') ?: 'localhost',
                        'port'      => getenv('DB_PORT') ?: 3306,
                        'schema'    => getenv('DB_SCHEMA'),
                        'charset'   => getenv('DB_CHARSET'),
                        'attributes' => [
                            PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                        ]
                    ]
                ],
                'dadata' => [
                    'token'    => getenv('DADATA_TOKEN'),
                    'base_url' => "https://suggestions.dadata.ru/suggestions/api/4_1/rs/"
                ]
            ]);
        }
    ]);
};
