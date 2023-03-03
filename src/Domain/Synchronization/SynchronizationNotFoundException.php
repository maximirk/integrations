<?php

declare(strict_types=1);

namespace App\Domain\Synchronization;

use App\Domain\DomainException\DomainRecordNotFoundException;

class SynchronizationNotFoundException extends DomainRecordNotFoundException
{
    /**
     * @var string
     */
    public $message = 'Такой синхронизации не существует.';
}
