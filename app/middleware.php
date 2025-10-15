<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\TwigMiddleware;

return static function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(TwigMiddleware::create($app, $app->getContainer()?->get('view')));
};
