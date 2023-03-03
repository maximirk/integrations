<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\Service\Dto\DtoService;
use App\Service\Dto\ProductDto;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Predis\ClientInterface;
use Generator;

class RedisProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected ClientInterface $redis, protected DtoService $dtoService)
    {
    }

    /**
     * @throws AssertionFailedException
     */
    public function get(string $id, string $table): ?Product
    {
        if (!$this->redis->hexists($table, $id)) {
            return null;
        }

        $product = $this->redis->hget($table, $id);
        $product = $product ? json_decode($product, true) : '';
        $productDto = $this->dtoService->createProductDto($product);

        return new Product($productDto);
    }

    /**
     * @throws AssertionFailedException
     */
    public function add(ProductDto $product, string $id, string $table): void
    {
        Assertion::notEmpty($product->id);

        $data = json_encode(new Product($product)) ?: '';

        $this->redis->hset($table, $id, $data);
    }

    /**
     * @throws AssertionFailedException
     */
    public function update(ProductDto $product, string $id, string $table): ?bool
    {
        if (!$this->redis->hexists($table, $id)) {
            return null;
        }

        $this->add($product, $id, $table);
        return true;
    }

    public function deleteAll(string $table): void
    {
        $fields = $this->redis->hkeys($table);

        empty($fields) ?: $this->redis->hdel($table, $fields);
    }

    /**
     * @throws AssertionFailedException
     */
    public function getAll(string $table): array
    {
        $results = $this->redis->hgetall($table);

        $products = array();
        foreach ($results as $key => $result) {
            $productDto = $result ? $this->dtoService->createProductDto(json_decode($result, true)) : "";
            !$productDto ?: $products[$key] = new Product($productDto);
        }

        return $products;
    }

    /**
     * @throws AssertionFailedException
     */
    public function getAllGenerator(string $table): Generator
    {
        $cursor = 0;
        do {
            $results = $this->redis->hscan($table, $cursor);
            $cursor = $results[0];

            if (empty($results[1]) || !is_array($results[1])) {
                continue;
            }

            foreach ($results[1] as $key => $product) {
                $productDto = $product ? $this->dtoService->createProductDto(json_decode($product, true)) : "";
                !$productDto ?: yield $key => new Product($productDto);
            }
        } while ($cursor > 0);

        if (!isset($productDto) || !$productDto) {
            yield;
        }
    }

    public function llen(string $table): int
    {
        return $this->redis->hlen($table);
    }

    public function getRandKey(string $table): array
    {
        return $this->redis->hrandfield($table);
    }
}
