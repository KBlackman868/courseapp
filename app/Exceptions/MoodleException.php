<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MoodleException extends Exception
{
    public function __construct(
        string $message = "",
        private string $errorCode = 'moodle_error',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}