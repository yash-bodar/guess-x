<?php

namespace App\Exceptions;

use Exception;

class GameAlreadyFinishedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * // YB - 15-09-2026 Typed exception for attempting guess on finished game
     */
    public function __construct(string $status)
    {
        parent::__construct("This game has already ended with status: {$status}.", 400);
    }
}
