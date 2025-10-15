<?php

declare(strict_types=1);

use App\Domain\AddressRequest\AddressRequestRepository;
use App\Infrastructure\Persistence\AddressRequest\DatabaseAddressRequestRepository;
use DI\ContainerBuilder;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AddressRequestRepository::class => \DI\autowire(DatabaseAddressRequestRepository::class),
    ]);
};
