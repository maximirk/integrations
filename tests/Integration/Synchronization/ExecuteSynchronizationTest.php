<?php
declare(strict_types=1);

namespace App\Test\Integration\Synchronization;

use App\Domain\Synchronization\Synchronization;
use App\Domain\Synchronization\SynchronizationRepositoryInterface;
use App\Service\Stack\StackInterface;
use App\Service\Synchronization\BaseSynchronization;
use App\Service\Synchronization\SynchronizationService;
use App\Test\AppTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UnexpectedValueException;

final class ExecuteSynchronizationTest extends TestCase
{
    use AppTrait;

    protected ContainerInterface $container;
    protected StackInterface $stack;
    protected SynchronizationRepositoryInterface $synchronizationRepository;
    protected Synchronization $synchronizationEntity;

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
    }

    public function testExecuteTaskSynchronizationWithWrongAction(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->synchronizationRepository = $this->createStub(SynchronizationRepositoryInterface::class);
        $this->synchronizationRepository->method("findOfIdentifier")->willReturn($this->synchronizationEntity);

        $synchronizationIdentifier = $this->synchronizationEntity->getIdentifier();

        //записать задачу с неправильным действием
        $synchronizationStack =  BaseSynchronization::PREFIX_TABLE . ":$synchronizationIdentifier";
        $this->stack->setNameStack($synchronizationStack);
        $task = json_encode(array(
            'action' => "Go",
            'data' => ['synchronization_identifier' => $synchronizationIdentifier]
        ), JSON_THROW_ON_ERROR);
        $this->stack->push($task);

        $synchronization = $this->container->get(SynchronizationService::class);
        $synchronization->boot($this->synchronizationRepository);
        $synchronization->startTaskFromStack($synchronizationStack);
    }
}
