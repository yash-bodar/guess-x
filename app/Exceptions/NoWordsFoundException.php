<?php

namespace App\Exceptions;

use Exception;

class NoWordsFoundException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * // YB - 15-09-2026 Typed exception when no words are found in the dictionary for given length
     */
    public function __construct(int $length)
    {
        parent::__construct("No valid {$length}-letter words found in dictionary. Please run word seeder.", 500);
    }
}
