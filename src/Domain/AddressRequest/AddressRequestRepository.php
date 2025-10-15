<?php

namespace App\Domain\AddressRequest;

interface AddressRequestRepository
{
    public function add(AddressRequest $addressRequest): AddressRequest;
}
