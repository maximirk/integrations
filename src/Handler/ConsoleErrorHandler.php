<?php

namespace App\Handler;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use Throwable;

final class ConsoleErrorHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->addFileHandler('error.log')->createLogger();
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): void {
        if ($logErrors) {
            $messageError = json_encode(
                $this->getErrorDetails($exception, $logErrorDetails),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );
            $this->logger->error($messageError);

            if ($displayErrorDetails) {
                echo $messageError;
            }
        }
    }

    private function getErrorDetails(Throwable $exception, bool $errorDetails): array
    {
        if ($errorDetails === true) {
            return [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'previous' => $exception->getPrevious(),
                'trace' => $exception->getTrace(),
            ];
        }

        return [
            'message' => $exception->getMessage(),
        ];
    }
}
