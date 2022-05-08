<?php

namespace FruiVita\Corporate;

use FruiVita\Corporate\Contracts\IImportable;
use FruiVita\Corporate\Exceptions\FileNotReadableException;
use FruiVita\Corporate\Exceptions\UnsupportedFileTypeException;
use FruiVita\Corporate\Importer\DepartmentImporter;
use FruiVita\Corporate\Importer\DutyImporter;
use FruiVita\Corporate\Importer\OccupationImporter;
use FruiVita\Corporate\Importer\UserImporter;
use FruiVita\Corporate\Trait\Logable;
use Illuminate\Support\Facades\Log;

class Corporate implements IImportable
{
    use Logable;

    /**
     * Full path to the XML file with the corporate structure that will be
     * imported.
     *
     * @var string
     */
    protected $file_path;

    /**
     * Mime types supported.
     *
     * @var string[]
     */
    protected $mime_type = ['application/xml', 'text/xml'];

    /**
     * {@inheritdoc}
     */
    public function import(string $file_path)
    {
        throw_if(! $this->isReadable($file_path), FileNotReadableException::class);
        throw_if(! $this->allowedMimeType($file_path), UnsupportedFileTypeException::class);

        $this
            ->setFilePath($file_path)
            ->start()
            ->run()
            ->finish();
    }

    /**
     * Checks if the given file exists and can be read.
     *
     * @param string $file_path full path
     *
     * @return bool
     */
    private function isReadable(string $file_path)
    {
        $response = is_readable($file_path);

        clearstatcache();

        return $response;
    }

    /**
     * Checks if the mime type of the file is allowed.
     *
     * @param string $file_path full path
     *
     * @return bool
     */
    private function allowedMimeType(string $file_path)
    {
        return in_array(
            needle: mime_content_type($file_path),
            haystack: $this->mime_type
        );
    }

    /**
     * Set the file path of the file to be imported.
     *
     * @param string $file_path full path
     *
     * @return static
     */
    private function setFilePath(string $file_path)
    {
        $this->file_path = $file_path;

        return $this;
    }

    /**
     * Prepare the import.
     *
     * @return static
     */
    private function start()
    {
        if ($this->shouldLog()) {
            Log::log(
                level: $this->level,
                message: __('Start of corporate structure import'),
                context: [
                    'file_path' => $this->file_path,
                ]
            );
        }

        return $this;
    }

    /**
     * Run the import.
     *
     * @return static
     */
    private function run()
    {
        OccupationImporter::make()->import($this->file_path);
        DutyImporter::make()->import($this->file_path);
        DepartmentImporter::make()->import($this->file_path);
        UserImporter::make()->import($this->file_path);

        return $this;
    }

    /**
     * Finishes the import.
     *
     * @return static
     */
    private function finish()
    {
        if ($this->shouldLog()) {
            Log::log(
                level: $this->level,
                message: __('End of corporate structure import'),
                context: [
                    'file_path' => $this->file_path,
                ]
            );
        }

        return $this;
    }
}
