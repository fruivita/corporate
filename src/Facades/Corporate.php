<?php

namespace FruiVita\Corporate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FruiVita\Corporate\Corporate
 * @see https://laravel.com/docs/9.x/facades
 */
class Corporate extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'corporate';
    }
}
