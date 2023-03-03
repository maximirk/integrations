<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Service\Dto\ProductDto;
use JsonSerializable;

class Product implements JsonSerializable
{
    private string $id;
    private ?string $sku;
    private ?float $quantity;
    private ?string $type;
    private ?string $modificationId;
    private ?string $barcode;
    private ?array $modificationCharacteristics;
    private bool $archived;
    private ?float $price;

    public function __construct(ProductDto $productDto)
    {
        $this->id = $productDto->id;
        $this->sku = $productDto->sku;
        $this->quantity = $productDto->quantity;
        $this->type = $productDto->type;
        $this->modificationId = $productDto->modificationId;
        $this->barcode = $productDto->barcode;
        $this->modificationCharacteristics = $productDto->modificationCharacteristics;
        $this->archived = $productDto->archived;
        $this->price = $productDto->price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getModificationId(): ?string
    {
        return $this->modificationId;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getModificationCharacteristics(): ?array
    {
        return $this->modificationCharacteristics;
    }

    public function getArchived(): bool
    {
        return $this->archived;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'type' => $this->type,
            'modificationId' => $this->modificationId,
            'barcode' => $this->barcode,
            'modificationCharacteristics' => $this->modificationCharacteristics,
            'archived' => $this->archived,
            'price' => $this->price
        ];
    }
}
