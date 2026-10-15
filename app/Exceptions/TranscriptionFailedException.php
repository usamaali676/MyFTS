<?php

namespace App\Exceptions;

class TranscriptionFailedException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $httpStatus = 500)
    {
        parent::__construct($message);
    }
}
