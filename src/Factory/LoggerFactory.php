<?php

namespace App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final class LoggerFactory
{
    private string $path;
    private int $level;
    private array $handler = [];
    private ?LoggerInterface $testLogger;

    public function __construct(array $settings = [])
    {
        $this->path = (string)($settings['path'] ?? '');
        $this->level = (int)($settings['level'] ?? Logger::DEBUG);

        // This can be used for testing to make the Factory testable
        if (isset($settings['test'])) {
            $this->testLogger = $settings['test'];
        }
    }

    public function createLogger(string $name = null): LoggerInterface
    {
        if (isset($this->testLogger)) {
            return $this->testLogger;
        }

        $logger = new Logger($name ?: Uuid::v4()->toRfc4122());

        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }

        $this->handler = [];

        return $logger;
    }

    public function addHandler(HandlerInterface $handler): self
    {
        $this->handler[] = $handler;

        return $this;
    }

    public function addFileHandler(string $filename, int $level = null): self
    {
        $filename = sprintf('%s/%s', $this->path, $filename);
        /** @phpstan-ignore-next-line */
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level ?? $this->level, true, 0777);

        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, true, true));

        $this->addHandler($rotatingFileHandler);

        return $this;
    }
}
