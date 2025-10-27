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
     * @return array{
     *     full_address: string, city_district: string, city_area: string, street: string, house: string
     * }
     */
    public function search(string $searchAddress): array
    {
        $result = [];

        try {
            if (!empty($searchAddress)) {
                // Ограничение по городу
                $moscow_restrictions = [
                    "locations" => ["kladr_id" => "77"]
                ];

                $dadata_result = $this->searchByAddressLine($searchAddress, $moscow_restrictions);

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
                    $data = $address['data'];
                    // TODO Move to some Resource file
                    $result[] = [
                        'full_address'  => $address['unrestricted_value'],
                        'city_district' => $data['city_district_with_type'] ?? 'Не заполнено',
                        'city_area'     => $data['city_area'] ?? 'Не заполнено', // район
                        'street'        => $data['street_with_type'] ?? 'Не заполнено',
                        'house'         => $data['house'] ?? 'Не заполнено'
                    ];
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
    private function searchByAddressLine(string $searchTerm, array $args = []): array
    {
        return $this->dadataClient->suggest(
            name: DadataClient::ADDRESS_SEARCH_TYPE,
            query: $searchTerm,
            kwargs: $args
        );
    }
}
