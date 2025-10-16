<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Persistence\AddressRequest\DatabaseAddressRequestRepository;
use App\Services\DadataClient;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

/**
 * Here we define all app dependencies for next injections
 *
 * @param ContainerBuilder $containerBuilder
 * @return void
 */
return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        'view' => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $twigSettings = $settings->get('twigger');

            return Twig::create($twigSettings['path'], $twigSettings['settings']);
        },
        DadataClient::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dadataSettings = $settings->get('dadata');

            return new DadataClient($dadataSettings['base_url'], $dadataSettings['token']);
        },
        PDO::class => function (ContainerInterface $c) {
            $logger = $c->get(LoggerInterface::class);

            try {
                $settings = $c->get(SettingsInterface::class);
                $databaseSettings = $settings->get('database');

                $connectionName = $databaseSettings['default'];

                $dsn = "$connectionName:host={$databaseSettings[$connectionName]['host']}"
                    . ";dbname={$databaseSettings[$connectionName]['schema']}";

                return new PDO(
                    $dsn,
                    $databaseSettings[$connectionName]['user'],
                    $databaseSettings[$connectionName]['password']
                );

            } catch (PdoException $e) {
                $logger->error($e->getMessage());
            }

            return null;
        },
        DatabaseAddressRequestRepository::class => function (ContainerInterface $c) {
            $logger = $c->get(LoggerInterface::class);
            return new DatabaseAddressRequestRepository($c->get(PDO::class), $logger);
        }
    ]);
};
