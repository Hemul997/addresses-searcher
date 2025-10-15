<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;
use function DI\env;

return static function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => !(getenv('ENVIRONMENT') === 'production'), // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'twigger' => [
                    'path'  => __DIR__ . '/../storage/templates',
                    'settings' => [
                        //'cache' => __DIR__ . '/../var/cache/twig',
                    ]
                ],
                'databases' => [
                    'default' => 'mysql',

                    'mysql' => [
                        'name'      => getenv('DB_DATABASE_NAME'),
                        'user'      => getenv('DB_USERNAME'),
                        'password'  => getenv('DB_PASSWORD'),
                        'host'      => getenv('DB_HOST'),
                        'port'      => getenv('DB_PORT'),
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
