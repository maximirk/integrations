<?php

declare(strict_types=1);

namespace App\Worker;

use App\Handler\ConsoleErrorHandler;
use App\Service\Synchronization\SynchronizationService;
use Psr\Log\LoggerInterface;
use Throwable;

class SynchronizationStackWorker
{
    public function __construct(
        protected SynchronizationService $synchronization,
        protected LoggerInterface     $logger,
        protected ConsoleErrorHandler $errorHandler,
        protected array               $settings = []
    ) {
    }

    public function start(string $stackName): void
    {
        $this->logger->info("Включение worker для задач синхронизации. Стек - $stackName");

        /** @phpstan-ignore-next-line */
        while (true) {
            try {
                $this->synchronization->startTaskFromStack($stackName);

                sleep(3);
            } catch (Throwable $exception) {
                call_user_func(
                    $this->errorHandler,
                    $exception,
                    (bool)$this->settings["error"]["display_error_details"],
                    (bool)$this->settings["error"]["log_errors"],
                    (bool)$this->settings["error"]["log_error_details"]
                );

                sleep(30);
            }
        }
    }
}
