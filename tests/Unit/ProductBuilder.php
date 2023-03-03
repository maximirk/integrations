<?php

declare(strict_types=1);

namespace App\Test\Unit;

use App\Domain\Product\Product;
use App\Service\Dto\DtoService;
use Assert\AssertionFailedException;

class ProductBuilder
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

    public function __construct()
    {
        $this->id = "id100";
        $this->sku = null;
        $this->quantity = 10.0;
        $this->type = null;
        $this->modificationId = null;
        $this->barcode = "1000100010001";
        $this->modificationCharacteristics = null;
        $this->archived = false;
        $this->price = 200.0;
    }

    /**
     * @throws AssertionFailedException
     */
    public function build(): Product
    {
        return new Product((new DtoService())->createProductDto([
            "id" => $this->id,
            "sku" => $this->sku,
            "quantity" => $this->quantity,
            "type" => $this->type,
            "modificationId" => $this->modificationId,
            "barcode" => $this->barcode,
            "modificationCharacteristics" => $this->modificationCharacteristics,
            "archived" => $this->archived,
            "price" => $this->price,
        ]));
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withBarcode(string $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function withQuantity(float $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
}
