<?php

declare(strict_types=1);

namespace App\Service\Stack;

interface StackInterface
{
    public function setNameStack(string $nameStack): void;
    public function push(string $data): void;
    public function pop(): ?string;
    public function llen(): int;
    public function lrange(int $start, int $end): array;
    public function peek(): ?string;
    public function clear(): void;
    public function reserved(): void;
    public function checkReserved(): bool;
    public function getFromReserved(): void;
    public function clearReserved(): void;
}
