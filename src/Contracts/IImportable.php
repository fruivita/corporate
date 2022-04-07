<?php

namespace FruiVita\Corporate\Contracts;

interface IImportable
{
    /**
     * Executa a importação.
     *
     * Importa, na sequência abaixo, as seguintes entidades:
     * 1. Occupation (Cargo)
     * 2. Duty (Função)
     * 3. Department (Lotação)
     * 4. User (Pessoa)
     *
     * @param string $file_path full path do arquivo XML com a estrutura
     *                          corporativa que será importado
     *
     * @throws \FruiVita\Corporate\Exceptions\FileNotReadableException
     * @throws \FruiVita\Corporate\Exceptions\UnsupportedFileTypeException
     *
     * @return void
     */
    public function import(string $file_path): void;
}
