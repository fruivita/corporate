<?php

namespace FruiVita\Corporate\Trait;

trait Logable
{
    /**
     * Nível do log.
     *
     * @var string
     */
    public $level = 'info';

    /**
     * Determina se deve-se logar o início e o fim do processo de importação.
     *
     * @return bool
     */
    public function shouldLog(): bool
    {
        return config('corporate.logging', false);
    }
}
