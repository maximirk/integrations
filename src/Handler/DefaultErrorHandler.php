<?php

namespace App\Handler;

use App\Factory\LoggerFactory;
use App\Renderer\JsonRenderer;
use DomainException;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

final class DefaultErrorHandler implements ErrorHandlerInterface
{
    private JsonRenderer $jsonRenderer;
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;

    public function __construct(
        JsonRenderer $jsonRenderer,
        ResponseFactoryInterface $responseFactory,
        LoggerFactory $loggerFactory
    ) {
        $this->jsonRenderer = $jsonRenderer;
        $this->responseFactory = $responseFactory;
        $this->logger = $loggerFactory
            ->addFileHandler('error.log')
            ->createLogger();
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        if ($logErrors) {
            $error = $this->getErrorDetails($exception, $logErrorDetails);
            $error['method'] = $request->getMethod();
            $error['url'] = (string)$request->getUri();

            $this->logger->error(json_encode(
                $error,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            ));
        }

        $response = $this->responseFactory->createResponse();

        $response = $this->jsonRenderer->json($response, [
            'error' => $this->getErrorDetails($exception, $displayErrorDetails),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $response->withStatus($this->getHttpStatusCode($exception));
    }

    private function getHttpStatusCode(Throwable $exception): int
    {
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException) {
            $statusCode = (int)$exception->getCode();
        }

        if ($exception instanceof DomainException || $exception instanceof InvalidArgumentException) {
            $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        }

        $file = basename($exception->getFile());
        if ($file === 'CallableResolver.php') {
            $statusCode = StatusCodeInterface::STATUS_NOT_FOUND;
        }

        return $statusCode;
    }

    private function getErrorDetails(Throwable $exception, bool $displayErrorDetails): array
    {
        if ($displayErrorDetails === true) {
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
