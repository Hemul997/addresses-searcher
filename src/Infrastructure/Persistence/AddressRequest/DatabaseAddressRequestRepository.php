<?php

namespace App\Infrastructure\Persistence\AddressRequest;

use App\Application\Settings\SettingsInterface;
use App\Domain\AddressRequest\AddressRequest;
use App\Domain\AddressRequest\AddressRequestRepository;

class DatabaseAddressRequestRepository implements AddressRequestRepository
{
    public function __construct(SettingsInterface $settings)
    {

    }

    public function add(AddressRequest $addressRequest): AddressRequest
    {
        // TODO: Implement add() method.
    }
}