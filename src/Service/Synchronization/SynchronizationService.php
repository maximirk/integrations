<?php

declare(strict_types=1);

namespace App\Service\Synchronization;

use App\Factory\SynchronizationTaskFactory;
use App\Service\Stack\StackInterface;
use Exception;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnexpectedValueException;

class SynchronizationService extends BaseSynchronization
{
    public function __construct(
        protected SynchronizationTaskFactory $taskFactory,
        protected StackInterface $stack,
        protected array $settings = []
    ) {
    }

    /**
     * Точка старта для всех синхронизаций. Создаются задачи в стек, для дальнейшего выполнения воркером.
     * @throws JsonException
     */
    public function start(string $synchronizationIdentifier): void
    {
        $synchronization = $this->synchronizationRepository->findOfIdentifier($synchronizationIdentifier);

        $synchronizationStack = parent::PREFIX_TABLE . ":$synchronizationIdentifier";

        $this->stack->setNameStack($synchronizationStack);
        $this->stack->clear();
        $this->stack->clearReserved();

        //To
        $this->createTask($synchronization->getTo(), $synchronizationIdentifier, "To");

        //From
        $this->createTask($synchronization->getFrom(), $synchronizationIdentifier, "From");
    }

    /**
     * @throws JsonException
     */
    private function createTask(array $controlSystem, string $synchronizationIdentifier, string $action): void
    {
        //none - идентификатор для пропуска действия
        if ($controlSystem['identifier'] == "none") {
            return;
        }

        $task = json_encode(array(
            'action' => $action,
            'data' => ['synchronization_identifier' => $synchronizationIdentifier]
        ), JSON_THROW_ON_ERROR);

        $this->stack->push($task);
    }

    /**
     * Старт задачи синхронизации, по идентификатору и направлению действия.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function startTask(string $action, string $synchronizationIdentifier): void
    {
        $synchronization = $this->synchronizationRepository->findOfIdentifier($synchronizationIdentifier);

        $controlSystem = match ($action) {
            "To" => $synchronization->getTo(),
            "From" => $synchronization->getFrom(),
            default => throw new UnexpectedValueException("Нет такого действия.")
        };

        $taskExecutor = $this->taskFactory->createControlSystem(
            $synchronization->getType(),
            $action,
            $controlSystem['identifier']
        );

        $taskData = $synchronization->jsonSerialize();

        $taskExecutor->execute($taskData);
    }

    /**
     * Старт текущей задачи в нужном стеке синхронизации
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function startTaskFromStack(string $stackName): void
    {
        $this->stack->setNameStack($stackName);

        $this->stack->checkReserved() ? $this->stack->getFromReserved() : $this->stack->reserved();

        $dataStack = $this->stack->pop();
        $dataStack ? $message = json_decode($dataStack, true) : $message = null;

        if (!empty($message['action']) && !empty($message['data']['synchronization_identifier'])) {
            $this->startTask($message['action'], $message['data']['synchronization_identifier']);
        }

        $this->stack->clearReserved();
    }
}
