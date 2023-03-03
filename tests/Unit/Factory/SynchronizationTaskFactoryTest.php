<?php
declare(strict_types=1);

namespace App\Test\Unit\Factory;

use App\Factory\SynchronizationTaskFactory;
use App\Service\ControlSystem\BaseControlSystem;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;
use Psr\Container\ContainerInterface;

final class SynchronizationTaskFactoryTest extends TestCase
{
    public ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = $this->createStub(ContainerInterface::class);
    }

    public function testCreateControlSystemSuccess(): void
    {
        $controlSystem = $this->createStub(BaseControlSystem::class);
        $this->container->method('get')->willReturn($controlSystem);

        $factory = new SynchronizationTaskFactory($this->container);
        $result = $factory->createControlSystem("Stock", "From", "MoySklad");
        $this->assertSame($controlSystem, $result);
    }

    public function testCreateControlSystemException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $factory = new SynchronizationTaskFactory($this->container);
        $factory->createControlSystem("Stock", "From", "NoMoySklad");
    }
}
