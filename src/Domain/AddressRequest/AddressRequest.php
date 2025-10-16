<?php

namespace App\Domain\AddressRequest;

use Carbon\Carbon;

class AddressRequest implements \JsonSerializable
{
    public static string $table = 'address_requests';

    public function __construct(private string $search_text, private Carbon $created_at, private ?int $id = 0)
    {
    }

    public function setId(?int $id): void
    {
        if ($this->id === 0) {
            $this->id = $id;
        }
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchText(): string
    {
        return $this->search_text;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->id ?? 0,
            'search_text' => $this->search_text,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
