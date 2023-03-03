<?php

namespace App\Domain\ControlSystem;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ControlSystemNotFoundException extends DomainRecordNotFoundException
{
    /**
     * @var string
     */
    public $message = 'Такой системы не существует.';
}
