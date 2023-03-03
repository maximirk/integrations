<?php

declare(strict_types=1);

namespace App\Service\Stack;

use Predis\ClientInterface;

class RedisStack implements StackInterface
{
    protected ClientInterface $client;
    protected string $nameStack;

    public function __construct(ClientInterface $redis)
    {
        $this->client = $redis;
    }

    public function setNameStack(string $nameStack): void
    {
        $this->nameStack = $nameStack;
    }

    public function push(string $data): void
    {
        $this->client->lpush($this->nameStack, array($data));
    }

    public function pop(): ?string
    {
        return $this->client->lpop($this->nameStack);
    }

    public function llen(): int
    {
        return $this->client->llen($this->nameStack);
    }

    public function lrange(int $start, int $end): array
    {
        return $this->client->lrange($this->nameStack, $start, $end);
    }

    public function peek(): ?string
    {
        $data = $this->pop();
        if ($data) {
            $this->push($data);
            return $data;
        }

        return null;
    }

    public function clear(): void
    {
        $this->client->del($this->nameStack);
    }

    public function reserved(): void
    {
        $llen = $this->client->llen($this->nameStack);
        $list = $this->client->lrange($this->nameStack, 0, $llen);
        empty($list) ?: $this->client->lpush($this->nameStack . ":reserved", array_reverse($list));
    }

    public function checkReserved(): bool
    {
        $llen = $this->client->llen($this->nameStack . ":reserved");

        if ($llen) {
            return true;
        } else {
            return false;
        }
    }

    public function getFromReserved(): void
    {
        $checkReserved = $this->checkReserved();
        if ($checkReserved) {
            $llen = $this->client->llen($this->nameStack . ":reserved");
            $list = $this->client->lrange($this->nameStack . ":reserved", 0, $llen);
            $this->clear();
            empty($list) ?: $this->client->lpush($this->nameStack, array_reverse($list));
        }
    }

    public function clearReserved(): void
    {
        $this->client->del($this->nameStack . ":reserved");
    }
}
