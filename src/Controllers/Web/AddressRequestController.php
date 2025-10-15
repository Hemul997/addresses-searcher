<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Domain\AddressRequest\AddressRequestRepository;
use App\Services\AddressRequestService;
use App\Services\DadataClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class AddressRequestController extends BaseController
{
    private AddressRequestService $service;

    public function __construct(ContainerInterface $container)
    {
        $this->service = new AddressRequestService(
            $container->get(AddressRequestRepository::class),
            $container->get(DadataClient::class),
            $container->get(LoggerInterface::class)
        );
        parent::__construct($container);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        /** @var Twig $view */
        $view = $this->container->get('view');
        /** @var LoggerInterface $logger */
        $logger = $this->container->get(LoggerInterface::class);

        $logger->info('Page were open');

        return $view->render(
            $response,
            'addresses/search.html.twig',
            ['page_title' => 'Поиск адресов', 'addresses' => $args['addresses'] ?? []]
        );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function search(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $result = $this->service->search($data);

        return $this->index($request, $response, ['addresses' => $result]);
    }
}
