<?php

namespace FruiVita\Corporate\Exceptions;

use Exception;

/**
 * Tipo de arquivo não suportado.
 *
 * @see https://laravel.com/docs/9.x/errors
 */
class UnsupportedFileTypeException extends Exception
{
    public function __construct()
    {
        parent::__construct(__('corporate::The file must be in [:attribute] format!', ['attribute' => 'XML']));
    }
}
