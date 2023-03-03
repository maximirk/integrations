<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Service\Dto\ProductDto;
use Generator;

interface ProductRepositoryInterface
{
    public function get(string $id, string $table): ?Product;
    public function add(ProductDto $product, string $id, string $table): void;
    public function update(ProductDto $product, string $id, string $table): ?bool;
    public function deleteAll(string $table): void;
    public function getAll(string $table): array;
    public function getAllGenerator(string $table): Generator;
    public function llen(string $table): int;
    public function getRandKey(string $table): array;
}
