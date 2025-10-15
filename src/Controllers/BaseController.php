<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;

abstract class BaseController
{
    public function __construct(protected ContainerInterface $container)
    {
    }
}
