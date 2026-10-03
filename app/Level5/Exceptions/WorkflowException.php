<?php

namespace App\Level5\Exceptions;

use RuntimeException;

class WorkflowException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
