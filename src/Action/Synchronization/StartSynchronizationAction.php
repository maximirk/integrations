<?php

declare(strict_types=1);

namespace App\Action\Synchronization;

use App\Action\Action;
use Assert\Assertion;
use Assert\AssertionFailedException;
use App\Service\Synchronization\SynchronizationService;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class StartSynchronizationAction extends Action
{
    public function __construct(protected SynchronizationService $synchronization)
    {
    }

    /**
     * {@inheritdoc}
     * @throws AssertionFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $dataRequest = (array)$this->request->getParsedBody();
        Assertion::notEmpty($dataRequest['synchronization_identifier']);

        $this->synchronization->start((string)$dataRequest['synchronization_identifier']);

        return $this->response;
    }
}
