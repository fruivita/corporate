<?php

namespace FruiVita\Corporate\Importer\Contracts;

interface IImportable
{
    /**
     * Run the import.
     *
     * @param string $file_path full path of the XML file with the corporate
     *                          structure that will be imported
     *
     * @return void
     */
    public function import(string $file_path);
}
