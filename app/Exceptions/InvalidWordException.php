<?php

namespace App\Exceptions;

use Exception;

class InvalidWordException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * // YB - 15-09-2026 Typed exception for guess not found in dictionary
     */
    public function __construct(string $message = 'The entered word is not in the dictionary.')
    {
        parent::__construct($message, 422);
    }
}
