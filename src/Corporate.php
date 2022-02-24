<?php

namespace FruiVita\Corporate;

use FruiVita\Corporate\Contracts\IImportable;
use FruiVita\Corporate\Exceptions\FileNotReadableException;
use FruiVita\Corporate\Exceptions\UnsupportedFileTypeException;
use FruiVita\Corporate\Importer\DepartmentImporter;
use FruiVita\Corporate\Importer\DutyImporter;
use FruiVita\Corporate\Importer\OccupationImporter;
use FruiVita\Corporate\Importer\PersonImporter;
use FruiVita\Corporate\Trait\Logable;
use Illuminate\Support\Facades\Log;

class Corporate implements IImportable
{
    use Logable;

    /**
     * Path completo para o arquivo XML com a estrutura corporativa que será
     * importado.
     *
     * @var string
     */
    protected $file_path;

    /**
     * Mimes tipes suportados.
     *
     * @var string[]
     */
    protected $mime_type = ['application/xml', 'text/xml'];

    /**
     * {@inheritdoc}
     */
    public function import(string $file_path): void
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
     * Verfica se o arquivo informado existe e pode ser lido.
     *
     * @param string $file_path full path
     *
     * @return bool
     */
    private function isReadable(string $file_path): bool
    {
        $response = is_readable($file_path);

        clearstatcache();

        return $response;
    }

    /**
     * Verfica se o mime type do arquivo é permitido.
     *
     * @param string $file_path full path
     *
     * @return bool
     */
    private function allowedMimeType(string $file_path): bool
    {
        return in_array(
            needle: mime_content_type($file_path),
            haystack: $this->mime_type
        );
    }

    /**
     * Define o file path do arquivo que será importado.
     *
     * @param string $file_path full path
     *
     * @return static
     */
    private function setFilePath(string $file_path): static
    {
        $this->file_path = $file_path;

        return $this;
    }

    /**
     * Tratativas iniciais da importação.
     *
     * @return static
     */
    private function start(): static
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
     * Aciona os importadores.
     *
     * @return static
     */
    private function run(): static
    {
        OccupationImporter::make()->import($this->file_path);
        DutyImporter::make()->import($this->file_path);
        DepartmentImporter::make()->import($this->file_path);
        PersonImporter::make()->import($this->file_path);

        return $this;
    }

    /**
     * Tratativas finais da importação.
     *
     * @return static
     */
    private function finish(): static
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
