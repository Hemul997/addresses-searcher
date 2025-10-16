<?php

declare(strict_types=1);

use App\Controllers\Web\AddressRequestController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app) {
    $app->redirect('/', '/addresses/search');

    $app->group('/addresses', function (Group $group) {
        $group->get('/search', AddressRequestController::class . ':index');
        $group->post('/search', AddressRequestController::class . ':search');
    });
};
