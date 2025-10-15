<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Services\DadataClient;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'view' => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $twigSettings = $settings->get('twigger');

            return Twig::create($twigSettings['path'], $twigSettings['settings']);
        },
        DadataClient::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dadataSettings = $settings->get('dadata');

            return new DadataClient($dadataSettings['base_url'], $dadataSettings['token']);
        }
    ]);
};
