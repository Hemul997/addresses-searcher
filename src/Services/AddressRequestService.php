<?php

namespace App\Services;

use App\Domain\AddressRequest\AddressRequestRepository;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class AddressRequestService
{
    public const SEARCH_TYPE = 'address';

    public function __construct(
        private AddressRequestRepository $repository,
        private DadataClient $dadataClient,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param array $data
     * @return array{}
     */
    public function search(array $data): array
    {
        $result = [];

        try {
            if (!empty($data['address'])) {
                $search = $data['address'];
                $dadata_result = $this->searchAddress($search);
            }
            if (!empty($dadata_result)) {
                foreach ($dadata_result as $address) {
                    $result[] = $address['unrestricted_value'];
                }
            }
        } catch (\Exception | GuzzleException $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }


    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    private function searchAddress(string $searchTerm): array
    {
        return $this->dadataClient->suggest(self::SEARCH_TYPE, $searchTerm);
    }
}
