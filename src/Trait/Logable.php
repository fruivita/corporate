<?php

namespace FruiVita\Corporate\Trait;

trait Logable
{
    /**
     * Log level.
     *
     * @var string
     */
    public $level = 'info';

    /**
     * Determines whether to log the beginning and end of the import process.
     *
     * @return bool
     */
    public function shouldLog()
    {
        return config('corporate.logging', false);
    }
}
