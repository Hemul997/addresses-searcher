<?php

namespace App\Domain\AddressRequest;

class AddressRequest implements \JsonSerializable
{
    public function __construct(private ?int $id, private string $search_text, private string $created_at)
    {
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
     * @throws \JsonException
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->id ?? 0,
            'search_text' => $this->search_text,
            'created_at'  => $this->created_at
        ];
    }
}
