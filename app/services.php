<?php

use App\Domain\AddressRequest\AddressRequestRepository;
use App\Services\AddressRequestService;
use App\Services\DadataClient;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder) {
    /**
     * Here we define all data services
     *
     * @param ContainerBuilder $containerBuilder
     * @return void
     */
    $containerBuilder->addDefinitions([
        AddressRequestService::class => function (ContainerInterface $container) {
            return new AddressRequestService(
                $container->get(AddressRequestRepository::class),
                $container->get(DadataClient::class),
                $container->get(LoggerInterface::class)
            );
        }
    ]);
};
