<?php

declare(strict_types=1);

namespace App\Service\Synchronization;

use App\Domain\Synchronization\SynchronizationRepositoryInterface;
use DI\Attribute\Inject;

class BaseSynchronization
{
    public const PREFIX_TABLE = "synchronization";
    public SynchronizationRepositoryInterface $synchronizationRepository;

    #[Inject]
    public function boot(
        SynchronizationRepositoryInterface $synchronizationRepository
    ): void {
        $this->synchronizationRepository = $synchronizationRepository;
    }
}
