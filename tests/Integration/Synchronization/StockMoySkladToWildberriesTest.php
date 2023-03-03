<?php
declare(strict_types=1);

namespace App\Test\Integration\Synchronization;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\Synchronization\Synchronization;
use App\Domain\Synchronization\SynchronizationRepositoryInterface;
use App\Service\ControlSystem\BaseControlSystem;
use App\Service\Stack\StackInterface;
use App\Service\Synchronization\BaseSynchronization;
use App\Service\Synchronization\SynchronizationService;
use App\Test\AppTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class StockMoySkladToWildberriesTest extends TestCase
{
    use AppTrait;

    protected ContainerInterface $container;
    protected StackInterface $stack;
    protected ProductRepositoryInterface $productRepository;
    protected SynchronizationRepositoryInterface $synchronizationRepository;
    protected Synchronization $synchronizationEntity;
    protected string $synchronizationIdentifier;
    protected string $synchronizationStack;
    protected string $stockRepositoryName;
    protected string $productRepositoryName;

    public function setUp(): void
    {
        $this->container = $this->getContainer();

        $this->stack = $this->container->get(StackInterface::class);

        $settingsWb = $this->getWildberriesSettings();
        $settingsMs = $this->getMoySkladSettings();

        $this->synchronizationEntity = new Synchronization(
            1,
            "StockMainMoySkladToWildberriesTest",
            "Stock",
            [
                "identifier" => "MoySklad",
                "control_system_inner_id" => $settingsMs["storeIdTest"]
            ],
            [
                "identifier" => "Wildberries",
                "control_system_inner_id" => $settingsWb["storeIdTest"]
            ]
        );

        $this->synchronizationRepository = $this->createStub(SynchronizationRepositoryInterface::class);
        $this->synchronizationRepository->method("findOfIdentifier")->willReturn($this->synchronizationEntity);

        $this->synchronizationIdentifier = $this->synchronizationEntity->getIdentifier();
        $this->productRepository = $this->container->get(ProductRepositoryInterface::class);
        $this->synchronizationStack =  BaseSynchronization::PREFIX_TABLE . ":$this->synchronizationIdentifier";

        $controlSystemFrom = $this->synchronizationEntity->getFrom();
        $this->stockRepositoryName = BaseControlSystem::TABLE_STOCK_KEY_BARCODE
            . ":{$controlSystemFrom["identifier"]}"
            . ":store_{$controlSystemFrom["control_system_inner_id"]}";
        $this->productRepositoryName = BaseControlSystem::TABLE_PRODUCTS_KEY_PRODUCT_ID
            . ":{$controlSystemFrom["identifier"]}";
    }

    public function getProducts(): void
    {
        $synchronizationEntity = new Synchronization(
            2,
            "ProductsMoySkladTest",
            "Products",
            [
                "identifier" => "MoySklad",
                "control_system_inner_id" => ""
            ],
            [
                "identifier" => "none",
                "control_system_inner_id" => ""
            ]
        );

        $synchronizationRepository = $this->createStub(SynchronizationRepositoryInterface::class);
        $synchronizationRepository->method("findOfIdentifier")->willReturn($synchronizationEntity);

        $synchronizationIdentifier = $synchronizationEntity->getIdentifier();
        $synchronizationStack =  BaseSynchronization::PREFIX_TABLE . ":$synchronizationIdentifier";

        $synchronization = $this->container->get(SynchronizationService::class);
        $synchronization->boot($synchronizationRepository);
        $synchronization->start($synchronizationIdentifier);

        $synchronization->startTaskFromStack($synchronizationStack);
    }

    public function testExecuteTaskSynchronizationSuccess(): void
    {
        //для выполнения задания нужна таблица с товарами
        $this->getProducts();

        //создать задачи для синхронизации и записать их в стек
        $synchronization = $this->container->get(SynchronizationService::class);
        $synchronization->boot($this->synchronizationRepository);
        $synchronization->start($this->synchronizationIdentifier);

        //запуск первой задачи
        $synchronization->startTaskFromStack($this->synchronizationStack);

        //запуск второй задачи
        $synchronization->startTaskFromStack($this->synchronizationStack);

        //данные для проверки
        $quantityProducts = $this->productRepository->llen($this->stockRepositoryName);
        $randKey = $this->productRepository->getRandKey($this->stockRepositoryName);
        $product = !$randKey[0] ?: $this->productRepository->get($randKey[0], $this->stockRepositoryName);

        $this->assertGreaterThan(1, $quantityProducts);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame($randKey[0], $product->getBarcode());
    }

    public function tearDown(): void
    {
        $this->stack->setNameStack($this->synchronizationStack);
        $this->stack->clear();
        $this->productRepository->deleteAll($this->stockRepositoryName);
        $this->productRepository->deleteAll($this->productRepositoryName);
    }
}
