<?php

declare(strict_types=1);

namespace App\Domain\Synchronization;

interface SynchronizationRepositoryInterface
{
    public function get(int $id): Synchronization;

    public function add(Synchronization $synchronization): void;

    /**
     * @throws SynchronizationNotFoundException
     */
    public function findOfIdentifier(string $identifier): Synchronization;
}
