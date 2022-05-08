<?php

namespace FruiVita\Corporate\Contracts;

interface IImportable
{
    /**
     * Run the import.
     *
     * Import, in the sequence below, the following models:
     * 1. Occupation (Cargo)
     * 2. Duty (Função)
     * 3. Department (Lotação)
     * 4. User (Pessoa)
     *
     * @param string $file_path full path of the XML file with the corporate
     *                          structure that will be imported
     *
     * @throws \FruiVita\Corporate\Exceptions\FileNotReadableException
     * @throws \FruiVita\Corporate\Exceptions\UnsupportedFileTypeException
     *
     * @return void
     */
    public function import(string $file_path);
}
