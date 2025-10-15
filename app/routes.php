<?php

declare(strict_types=1);

use App\Controllers\Web\AddressRequestController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return static function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->redirect('/', '/addresses/search');

    $app->group('/addresses', function (Group $group) {
        $group->get('/search', AddressRequestController::class . ':index');
        $group->post('/search', AddressRequestController::class . ':search');
    });


    $app->get('/hi/{name}', function ($request, $response, array $args) {
        $name = $args['name'];
        $view_data = [
            'name' => $name,
        ];

        $view = Twig::fromRequest($request);

        $str = $view->fetchFromString('<p>Hi, my name is {{ name }}.</p>', $view_data);

        $response->getBody()->write($str);

        return $response;
    });
};
