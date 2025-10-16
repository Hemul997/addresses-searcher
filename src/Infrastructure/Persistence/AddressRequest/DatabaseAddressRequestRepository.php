<?php

namespace App\Infrastructure\Persistence\AddressRequest;

use App\Domain\AddressRequest\AddressRequest;
use App\Domain\AddressRequest\AddressRequestRepository;
use App\Domain\DomainException\DomainRecordCreatingError;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

/**
 *
 */
final class DatabaseAddressRequestRepository implements AddressRequestRepository
{
    public function __construct(private readonly PDO $pdo, private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param AddressRequest $addressRequest
     * @return AddressRequest
     * @throws DomainRecordCreatingError
     */
    public function add(AddressRequest $addressRequest): AddressRequest
    {
        try {
            $table_name = AddressRequest::$table;

            $sql = "INSERT INTO $table_name (search_text, created_at) VALUES (:search_text, :created_at)";

            $stmt = $this->pdo->prepare($sql);

            if (
                $stmt->execute([
                'search_text' => $addressRequest->getSearchText(),
                'created_at' => $addressRequest->getCreatedAt()->format('Y-m-d H:i:s')
                ])
            ) {
                $lastInsertId = $this->pdo->lastInsertId();
                $addressRequest->setId($lastInsertId);
                return $addressRequest;
            }
        } catch (PDOException $e) {
            throw new DomainRecordCreatingError($e->getMessage());
        }

        throw new DomainRecordCreatingError(
            'Не удалось создать запись с аттрибутами: '
            . json_encode($addressRequest->jsonSerialize(), JSON_UNESCAPED_UNICODE)
        );
    }

    /**
    * @inheritDoc
     */
    public function isUniqueSearchText(string $searchText): bool
    {
        try {
            $table_name = AddressRequest::$table;

            $sql = "SELECT COUNT(`id`) as `rows_count` FROM $table_name WHERE search_text = :search_text";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(
                ['search_text' => $searchText]
            );

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['rows_count'] === 0;
        } catch (PDOException $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}
