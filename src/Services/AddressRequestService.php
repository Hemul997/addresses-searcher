<?php

namespace App\Services;

use App\Domain\AddressRequest\AddressRequest;
use App\Domain\AddressRequest\AddressRequestRepository;
use App\Domain\DomainException\DomainException;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Log\LoggerInterface;

class AddressRequestService
{
    public function __construct(
        private readonly AddressRequestRepository $repository,
        private readonly DadataClient $dadataClient,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param string $searchAddress
     * @return array{string}
     */
    public function search(string $searchAddress): array
    {
        $result = [];

        try {
            if (!empty($searchAddress)) {
                $moscow_restrictions = [
                    "locations" =>
                    ["kladr_id" => "77"]
                ];

                $dadata_result = $this->searchAddress($searchAddress, $moscow_restrictions);

                if ($this->repository->isUniqueSearchText($searchAddress)) {
                    $model = new AddressRequest($searchAddress, Carbon::now());

                    try {
                        $inserted = $this->repository->add($model);

                        $this->logger->info(
                            'Запись с параметрами ' . json_encode($inserted, JSON_UNESCAPED_UNICODE)
                            . ' успешно создана.'
                        );
                    } catch (DomainException $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
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
     * @throws JsonException
     */
    private function searchAddress(string $searchTerm, array $args = []): array
    {
        return $this->dadataClient->suggest(
            name: DadataClient::ADDRESS_SEARCH_TYPE,
            query: $searchTerm,
            kwargs: $args
        );
    }
}
