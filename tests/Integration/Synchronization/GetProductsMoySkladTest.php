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

final class GetProductsMoySkladTest extends TestCase
{
    use AppTrait;

    protected ContainerInterface $container;
    protected StackInterface $stack;
    protected ProductRepositoryInterface $productRepository;
    protected SynchronizationRepositoryInterface $synchronizationRepository;
    protected Synchronization $synchronizationEntity;
    protected string $synchronizationIdentifier;
    protected string $synchronizationStack;
    protected string $productRepositoryName;

    public function setUp(): void
    {
        $this->container = $this->getContainer();

        $this->stack = $this->container->get(StackInterface::class);

        $this->synchronizationEntity = new Synchronization(
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

        $this->synchronizationRepository = $this->createStub(SynchronizationRepositoryInterface::class);
        $this->synchronizationRepository->method("findOfIdentifier")->willReturn($this->synchronizationEntity);

        $this->synchronizationIdentifier = $this->synchronizationEntity->getIdentifier();
        $this->productRepository = $this->container->get(ProductRepositoryInterface::class);
        $this->synchronizationStack =  BaseSynchronization::PREFIX_TABLE . ":$this->synchronizationIdentifier";

        $controlSystemFrom = $this->synchronizationEntity->getFrom();
        $this->productRepositoryName = BaseControlSystem::TABLE_PRODUCTS_KEY_PRODUCT_ID
            . ":{$controlSystemFrom["identifier"]}";
    }

    public function testExecuteTaskSynchronizationSuccess(): void
    {
        //создать задачи для синхронизации и записать их в стек
        $synchronization = $this->container->get(SynchronizationService::class);
        $synchronization->boot($this->synchronizationRepository);
        $synchronization->start($this->synchronizationIdentifier);

        //запуск первой задачи
        $synchronization->startTaskFromStack($this->synchronizationStack);

        //данные для проверки
        $quantityProducts = $this->productRepository->llen($this->productRepositoryName);
        $randKey = $this->productRepository->getRandKey($this->productRepositoryName);
        $product = !$randKey[0] ?: $this->productRepository->get($randKey[0], $this->productRepositoryName);

        $this->assertGreaterThan(0, $quantityProducts);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame($randKey[0], $product->getId());

        //чистка стека задач
        $this->stack->setNameStack($this->synchronizationStack);
        $this->stack->clear();

        //второй запуск. товары не должны дублироваться. таблица не очищается, новые значения заменяют старые.
        $synchronization->start($this->synchronizationIdentifier);
        $synchronization->startTaskFromStack($this->synchronizationStack);

        //данные для проверки
        $quantityProducts = $this->productRepository->llen($this->productRepositoryName);
        $randKey = $this->productRepository->getRandKey($this->productRepositoryName);
        $product = !$randKey[0] ?: $this->productRepository->get($randKey[0], $this->productRepositoryName);
        //для проверки отсутствия дублей
        $productsFromRepository = $this->productRepository->getAll($this->productRepositoryName);
        $productsFromRepositoryUnique = array_unique(array_map(
            fn (&$value) => $value = $value->getId(),
            $productsFromRepository
        ));

        $this->assertGreaterThan(1, $quantityProducts);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame($randKey[0], $product->getId());
        $this->assertSame(count($productsFromRepository), count($productsFromRepositoryUnique));
    }

    public function tearDown(): void
    {
        $this->stack->setNameStack($this->synchronizationStack);
        $this->stack->clear();
        $this->productRepository->deleteAll($this->productRepositoryName);
    }
}
