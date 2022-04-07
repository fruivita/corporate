<?php

namespace FruiVita\Corporate\Importer\Contracts;

interface IImportable
{
    /**
     * Executa a importação.
     *
     * @param string $file_path full path do arquivo XML com a estrutura
     *                          corporativa que será importado
     *
     * @return void
     */
    public function import(string $file_path);
}
