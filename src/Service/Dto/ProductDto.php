<?php

declare(strict_types=1);

namespace App\Service\Dto;

class ProductDto
{
    public string $id;
    public ?string $sku;
    public ?float $quantity;
    public ?string $type;
    public ?string $modificationId;
    public ?string $barcode;
    public ?array $modificationCharacteristics;
    public bool $archived = false;
    public ?float $price;
}
