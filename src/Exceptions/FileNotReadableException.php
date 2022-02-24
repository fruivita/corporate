<?php

namespace FruiVita\Corporate\Exceptions;

use Exception;

/**
 * Arquivo não pôde ser lido.
 *
 * @see https://laravel.com/docs/9.x/errors
 */
class FileNotReadableException extends Exception
{
    public function __construct()
    {
        parent::__construct(__('corporate::O arquivo informado não pôde ser lido!'));
    }
}
