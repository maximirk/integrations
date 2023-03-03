<?php

declare(strict_types=1);

namespace App\Console;

use App\Service\Synchronization\SynchronizationService;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

final class StartSynchronizationCommand extends Command
{
    protected SynchronizationService $synchronization;

    public function __construct(SynchronizationService $synchronization)
    {
        $this->synchronization = $synchronization;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('synchronization');
        $this->setDescription('Синхронизация данных из разных систем');
        $this->addArgument(
            'synchronization_identifier',
            InputArgument::REQUIRED,
            'Необходим идентификатор синхронизации!'
        );
    }

    /**
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $param_request['synchronization_identifier'] = $input->getArgument('synchronization_identifier');

        $this->synchronization->start((string)$param_request['synchronization_identifier']);

        return 0;
    }
}
