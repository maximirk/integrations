<?php
declare(strict_types=1);

namespace App\Test\Integration\Synchronization;

use App\Domain\Synchronization\Synchronization;
use App\Domain\Synchronization\SynchronizationNotFoundException;
use App\Domain\Synchronization\SynchronizationRepositoryInterface;
use App\Service\Stack\StackInterface;
use App\Service\Synchronization\BaseSynchronization;
use App\Service\Synchronization\SynchronizationService;
use App\Test\AppTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class StartSynchronizationTest extends TestCase
{
    use AppTrait;

    protected ContainerInterface $container;
    protected StackInterface $stack;
    protected SynchronizationRepositoryInterface $synchronizationRepository;
    protected Synchronization $synchronizationEntity;

    public function setUp(): void
    {
        $this->container = $this->getContainer();

        $this->synchronizationEntity = new Synchronization(
            2,
            "syncTest",
            "typeTest",
            [
                "identifier" => "fromTest",
                "control_system_inner_id" => ""
            ],
            [
                "identifier" => "toTest",
                "control_system_inner_id" => ""
            ]
        );
    }

    public function testCreateStackForTaskSuccess(): void
    {
        $this->stack = $this->container->get(StackInterface::class);

        $this->synchronizationRepository = $this->createStub(SynchronizationRepositoryInterface::class);
        $this->synchronizationRepository->method("findOfIdentifier")->willReturn($this->synchronizationEntity);

        $synchronizationIdentifier = $this->synchronizationEntity->getIdentifier();

        $synchronization = $this->container->get(SynchronizationService::class);
        $synchronization->boot($this->synchronizationRepository);
        $synchronization->start($synchronizationIdentifier);

        $synchronizationStack =  BaseSynchronization::PREFIX_TABLE . ":$synchronizationIdentifier";
        $this->stack->setNameStack($synchronizationStack);
        $firstTask = json_decode($this->stack->pop(), true);
        $secondTask = json_decode($this->stack->pop(), true);

        $this->stack->clear();

        $this->assertSame("From", $firstTask["action"]);
        $this->assertSame("To", $secondTask["action"]);
        $this->assertSame($synchronizationIdentifier, $firstTask["data"]["synchronization_identifier"]);
    }

    public function testCreateStackForTaskWrongSynchronizationIdentifier(): void
    {
        $this->expectException(SynchronizationNotFoundException::class);

        $synchronizationIdentifier = "wrongIdentifier";

        $synchronization = $this->container->get(SynchronizationService::class);
        $synchronization->start($synchronizationIdentifier);
    }
}
