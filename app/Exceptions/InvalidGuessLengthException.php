<?php

namespace App\Exceptions;

use Exception;

class InvalidGuessLengthException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * // YB - 15-09-2026 Typed exception for guess length mismatch
     */
    public function __construct(int $expectedLength, int $actualLength)
    {
        parent::__construct("Word must be exactly {$expectedLength} letters long (received {$actualLength}).", 422);
    }
}
