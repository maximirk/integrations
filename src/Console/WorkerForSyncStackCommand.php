<?php

declare(strict_types=1);

namespace App\Console;

use App\Service\Synchronization\BaseSynchronization;
use App\Worker\SynchronizationStackWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class WorkerForSyncStackCommand extends Command
{
    protected SynchronizationStackWorker $worker;

    public function __construct(SynchronizationStackWorker $worker)
    {
        $this->worker = $worker;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('sync_worker_stack');
        $this->setDescription('Worker для запуска задач синхронизации');
        $this->addArgument(
            'synchronization_identifier',
            InputArgument::REQUIRED,
            'Необходим идентификатор синхронизации!'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): never
    {
        $stackName = BaseSynchronization::PREFIX_TABLE . ":{$input->getArgument('synchronization_identifier')}";

        $this->worker->start($stackName);

        exit(0);
    }
}
