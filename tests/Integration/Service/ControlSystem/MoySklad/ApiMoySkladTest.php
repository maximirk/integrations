<?php
declare(strict_types=1);

namespace App\Test\Integration\Service\ControlSystem\MoySklad;

use App\Service\ControlSystem\MoySklad\ApiMoySklad;
use App\Test\AppTrait;
use Evgeek\Moysklad\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

final class ApiMoySkladTest extends TestCase
{
    use AppTrait;

    protected ApiMoySklad $api;
    protected array $moySkladSettings;

    public function setUp(): void
    {
        $this->moySkladSettings = $this->getMoySkladSettings();

        $this->api = new ApiMoySklad();
        $this->api->createClient($this->moySkladSettings["login"], $this->moySkladSettings["password"]);
    }

    public function testGetStockByStoreSuccess(): void
    {
        $result = $this->api->getStockByStore($this->moySkladSettings["storeIdTest"]);

        $this->assertSame($this->moySkladSettings["storeIdTest"], $result[0]["storeId"]);
    }

    public function testGetStockByStoreError(): void
    {
        $this->expectException(ApiException::class);

        $this->api->getStockByStore("wrongStoreId");
    }

    public function testGetModifications(): void
    {
        $result = $this->api->getModifications(3, 3);

        $this->assertIsString($result[0]["id"]);
        $this->assertSame(3, count($result));
    }

    public function testGetProducts(): void
    {
        $result = $this->api->getProducts(3, 3);

        $this->assertIsString($result[0]["id"]);
        $this->assertSame(3, count($result));
    }
}
