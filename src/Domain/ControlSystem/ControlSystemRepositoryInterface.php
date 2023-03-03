<?php

declare(strict_types=1);

namespace App\Domain\ControlSystem;

interface ControlSystemRepositoryInterface
{
    public function get(int $id): ControlSystem;

    public function add(ControlSystem $controlSystem): void;

    /**
     * @throws ControlSystemNotFoundException
     */
    public function findOfIdentifier(string $identifier): ControlSystem;
}
