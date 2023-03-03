<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\ControlSystem\ControlSystem;
use App\Domain\ControlSystem\ControlSystemNotFoundException;
use App\Domain\ControlSystem\ControlSystemRepositoryInterface;
use Assert\AssertionFailedException;
use Assert\Assertion;

class JsonControlSystemRepository implements ControlSystemRepositoryInterface
{
    protected array $settings;
    /** @var ControlSystem[] */
    protected array $controlSystems;
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

        $this->dataJson = $data["control_systems"] ?? array();

        foreach ($this->dataJson as $item) {
            Assertion::notEmpty($item['identifier']);
            Assertion::notEmpty($item['id']);

            $this->add(new ControlSystem(
                $item['identifier'],
                $item['id'],
                $item['login'],
                $item['password'],
                $item['apiKey'],
                $item['customFields']
            ));
        }
    }

    public function get(int $id): ControlSystem
    {
        if (!isset($this->controlSystems[$id])) {
            throw new ControlSystemNotFoundException();
        }
        return clone $this->controlSystems[$id];
    }

    public function add(ControlSystem $controlSystem): void
    {
        $this->controlSystems[$controlSystem->getId()] = $controlSystem;
    }

    public function findOfIdentifier(string $identifier): ControlSystem
    {
        foreach ($this->dataJson as $item) {
            $item['identifier'] !==  $identifier ?: $id = $item["id"];
        }

        if (!isset($id) || !isset($this->controlSystems[$id])) {
            throw new ControlSystemNotFoundException();
        }

        return $this->controlSystems[$id];
    }
}
