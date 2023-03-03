<?php
declare(strict_types=1);

namespace App\Test\Integration\Service\Stack;

use App\Service\Stack\StackInterface;
use App\Test\AppTrait;
use App\Test\Unit\ProductBuilder;
use Assert\AssertionFailedException;
use JsonException;
use PHPUnit\Framework\TestCase;

final class RedisStackTest extends TestCase
{
    use AppTrait;

    protected StackInterface $stack;
    protected string $testList = "unitRedisStackTest";

    public function setUp(): void
    {
        $container = $this->getContainer();
        $this->stack = $container->get(StackInterface::class);
        $this->stack->setNameStack($this->testList);
        $this->stack->clear();
    }

    public function testPush(): void
    {
        $products = $this->products();

        array_walk($products, fn ($product) => $this->stack->push($product));

        $llen = $this->stack->llen();
        $redisContent = $this->stack->lrange(0, $llen);

        $this->assertSame(3, $llen);
        array_walk($products, fn ($product) => $this->assertContains($product, $redisContent));
    }

    public function testPop(): void
    {
        $products = $this->products();

        array_walk($products, fn ($product) => $this->stack->push($product));

        $productPop = $this->stack->pop();

        $llen = $this->stack->llen();
        $redisContent = $this->stack->lrange(0, $llen);

        $this->assertSame(2, $llen);
        $this->assertSame($products[2], $productPop);

        unset($products[2]);
        array_walk($products, fn ($product) => $this->assertContains($product, $redisContent));
    }

    public function testPeek(): void
    {
        $products = $this->products();

        array_walk($products, fn ($product) => $this->stack->push($product));

        $productPeek = $this->stack->peek();

        $llen = $this->stack->llen();
        $redisContent = $this->stack->lrange(0, $llen);

        $this->assertSame(3, $llen);
        $this->assertSame($products[2], $productPeek);
        array_walk($products, fn ($product) => $this->assertContains($product, $redisContent));
    }

    public function testReserved(): void
    {
        $products = $this->products();
        array_walk($products, fn ($product) => $this->stack->push($product));

        $this->stack->reserved();

        $this->stack->setNameStack($this->testList . ":reserved");

        $llen = $this->stack->llen();
        $redisContent = $this->stack->lrange(0, $llen);

        $this->stack->setNameStack($this->testList);
        $this->stack->clearReserved();

        $this->assertSame(3, $llen);
        array_walk($products, fn ($product) => $this->assertContains($product, $redisContent));
    }

    public function testGetFromReserved(): void
    {
        $products = $this->products();
        array_walk($products, fn ($product) => $this->stack->push($product));

        $this->stack->reserved();
        $this->stack->pop();
        $this->stack->pop();

        $llen = $this->stack->llen();
        $this->assertSame(1, $llen);

        $this->stack->getFromReserved();
        $this->stack->clearReserved();

        $llen = $this->stack->llen();
        $redisContent = $this->stack->lrange(0, $llen);
        $this->assertSame(3, $llen);
        $this->assertSame(array_reverse($products), $redisContent);
    }

    /**
     * @throws AssertionFailedException
     * @throws JsonException
     */
    public function products(): array
    {
        return [
            json_encode(
                (new ProductBuilder())->withId("id100")
                    ->withBarcode("bar100")
                    ->withQuantity(rand(0, 100))
                    ->build(),
                JSON_THROW_ON_ERROR
            ),
            json_encode(
                (new ProductBuilder())->withId("id200")
                    ->withBarcode("bar200")
                    ->withQuantity(rand(0, 100))
                    ->build(),
                JSON_THROW_ON_ERROR
            ),
            json_encode(
                (new ProductBuilder())->withId("id300")
                    ->withBarcode("bar300")
                    ->withQuantity(rand(0, 100))
                    ->build(),
                JSON_THROW_ON_ERROR
            )
        ];
    }
}
