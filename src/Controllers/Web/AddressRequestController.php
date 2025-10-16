<?php

namespace App\Controllers\Web;

use App\Application\Helpers\InputCleaner;
use App\Controllers\BaseController;
use App\Services\AddressRequestService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class AddressRequestController extends BaseController
{
    private AddressRequestService $service;
    private Twig $view;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get('view');
        $this->service = $container->get(AddressRequestService::class);
        parent::__construct($container);
    }

    /**
     * Метод для рендера основной страницы приложения
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        return $this->view->render(
            $response,
            'addresses/search.html.twig',
            ['page_title' => 'Поиск адресов', 'addresses' => $args['addresses'] ?? []]
        );
    }

    /**
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function search(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $address = InputCleaner::clean($data['address'] ?? '');

        $result = $this->service->search($address);

        return $this->index($request, $response, ['addresses' => $result]);
    }
}
