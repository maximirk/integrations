<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Synchronization\Synchronization;
use App\Domain\Synchronization\SynchronizationNotFoundException;
use App\Domain\Synchronization\SynchronizationRepositoryInterface;
use Assert\AssertionFailedException;
use Assert\Assertion;

class JsonSynchronizationRepository implements SynchronizationRepositoryInterface
{
    protected array $settings;
    /** @var Synchronization[] */
    protected array $synchronizations;
    protected array $dataJson;
    protected static string $dataFile = "resources/app-data/data.json";

    /**
     * @throws AssertionFailedException
     */
    public function __construct(array $settings = array())
    {
        $this->settings = $settings;

        $json_base = file_get_contents($this->settings['document_root'] . static::$dataFile);

        $data = $json_base ? json_decode($json_base, true) : array();

        $this->dataJson = $data["synchronization"] ?? array();

        foreach ($this->dataJson as $item) {
            Assertion::notEmpty($item['id']);
            Assertion::notEmpty($item['identifier']);
            Assertion::notEmpty($item['type']);
            Assertion::notEmpty($item['from']);
            Assertion::notEmpty($item['to']);

            Assertion::notEmpty($item['from']['identifier']);
            Assertion::notEmpty($item['to']['identifier']);

            $this->add(new Synchronization(
                $item['id'],
                $item['identifier'],
                $item['type'],
                $item['from'],
                $item['to']
            ));
        }
    }

    public function get(int $id): Synchronization
    {
        if (!isset($this->synchronizations[$id])) {
            throw new SynchronizationNotFoundException();
        }
        return clone $this->synchronizations[$id];
    }

    public function add(Synchronization $synchronization): void
    {
        $this->synchronizations[$synchronization->getId()] = $synchronization;
    }

    public function findOfIdentifier(string $identifier): Synchronization
    {
        foreach ($this->dataJson as $item) {
            $item['identifier'] !==  $identifier ?: $id = $item["id"];
        }

        if (!isset($id) || !isset($this->synchronizations[$id])) {
            throw new SynchronizationNotFoundException();
        }

        return $this->synchronizations[$id];
    }
}
