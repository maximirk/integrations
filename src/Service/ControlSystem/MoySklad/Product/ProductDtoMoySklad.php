<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\MoySklad\Product;

use App\Service\Dto\ProductDto;
use Assert\Assertion;
use Assert\AssertionFailedException;

class ProductDtoMoySklad
{
    /**
     * DTO товара из сущности "МойСклад" - "Товар"
     * @throws AssertionFailedException
     */
    public function createFromProduct(array $product, ?array $modification = null): ProductDto
    {
        Assertion::notEmpty($product["id"]);

        $productDto = new ProductDto();

        $productDto->sku = !empty($product["article"]) ? trim($product["article"]) : null;
        $productDto->quantity = $product["quantity"] ?? null;
        $productDto->type = $product["meta"]["type"] ?? null;

        if ($modification) {
            Assertion::notEmpty($modification["id"]);
            $productDto->id = $modification["id"];
            $productDto->modificationId = $modification["id"];
            $productDto->modificationCharacteristics = $modification["characteristics"] ?? null;
            $productDto->barcode = $this->getBarcode($modification);
            $productDto->archived = $modification["archived"] ?? false;
        } else {
            $productDto->id = $product["id"];
            $productDto->modificationId = null;
            $productDto->modificationCharacteristics = null;
            $productDto->barcode = $this->getBarcode($product);
            $productDto->archived = $product["archived"] ?? false;
        }

        $productDto->price = $product["price"] ?? null;


        return $productDto;
    }

    /**
     * DTO товара из "МойСклад" - "Отчет Остатки", где используется сущность "Ассортимент"
     * @throws AssertionFailedException
     */
    public function createFromStock(array $product): ProductDto
    {
        Assertion::notEmpty($product["id"]);

        $productDto = new ProductDto();

        $productDto->id = $product["id"];
        $productDto->sku = $product["article"] ?? null;
        $productDto->quantity = $product["quantity"] ?? null;
        $productDto->type = $product["meta"]["type"] ?? null;
        $productDto->modificationId = null;
        $productDto->barcode = $product["barcode"] ?? null;
        $productDto->modificationCharacteristics = null;
        $productDto->archived = false;
        $productDto->price = null;

        return $productDto;
    }


    /**
     * Может быть несколько баркодов, взять первый ean13
     */
    private function getBarcode(array $data): ?string
    {
        if (!isset($data["barcodes"])) {
            return null;
        }

        foreach ($data["barcodes"] as $barcodes) {
            $barcode = $barcodes["ean13"] ?? null;
            if (!is_null($barcode)) {
                return trim($barcode);
            }
        }

        return null;
    }
}
