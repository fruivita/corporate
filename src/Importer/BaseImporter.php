<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Importer\Contracts\IImportable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

abstract class BaseImporter implements IImportable
{
    /**
     * Rules that will be applied to each field of the XML node that will be
     * imported.
     *
     * @var array<string, mixed[]> assoc array
     */
    protected $rules;

    /**
     * Name of the XML node that will be imported.
     *
     * @var string
     */
    protected $node;

    /**
     * Attributes (fields) to consider the unique object in the database.
     *
     * @var string[]
     */
    protected $unique;

    /**
     * Attributes (fields) that must be updated in the database if the object
     * is already persisted.
     *
     * @var string[]
     */
    protected $fields_to_update;

    /**
     * Full path to the XML file with the corporate structure that will be
     * imported.
     *
     * @var string
     */
    protected $file_path;

    /**
     * Number of records that will be inserted/updated in a single query.
     *
     * @var int
     */
    protected $max_upsert;

    /**
     * {@inheritdoc}
     */
    public function import(string $file_path)
    {
        $this
            ->setFilePath($file_path)
            ->setMaxUpsert()
            ->run();
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
     * Set the number of records that will be inserted/updated in a single
     * query.
     *
     * @return static
     */
    private function setMaxUpsert()
    {
        $max = config('corporate.maxupsert');

        $this->max_upsert = (is_int($max) && $max >= 1)
                                ? $max
                                : 500;

        return $this;
    }

    /**
     * Extracts the fields of interest for the object from the XML node.
     *
     * The array will contain the fields of interest (key), and the respective
     * values extracted from the informed xml node.
     *
     * Ex.: [
     *     'id' => '10',
     *     'name' => 'foo',
     * ]
     *
     * @param \XMLReader $node node from which the values will be extracted
     *
     * @return array<string, string> array assoc
     */
    abstract protected function extractFieldsFromNode(\XMLReader $node);

    /**
     * Persistence of validated items.
     *
     * @param \Illuminate\Support\Collection $validated
     *
     * @return void
     */
    abstract protected function save(Collection $validated);

    /**
     * Places the XMLReader on the first XML node to be worked on.
     *
     * Ex: if the desire is to read the **foo**, the XML file will be read by
     * the **XMLReader**, returning the pointer pointing to the first node with
     * the name **foo** for them to be processed.
     *
     * @return \XMLReader
     *
     * @see https://drib.tech/programming/parse-large-xml-files-php
     */
    protected function startReadFrom()
    {
        $xml = new \XMLReader();
        $xml->open($this->file_path);

        // finding first primary element to work with
        while ($xml->read() && $xml->name != $this->node) {
        }

        return $xml;
    }

    /**
     * Executes the actual import.
     *
     * The execution is done through the following steps:
     * - Read the file;
     * - Extract the data from the xml node of interest;
     * - Validate the extracted data and, if necessary, log inconsistencies;
     * - Call the method responsible for persistence.
     *
     * @return static
     *
     * @see https://drib.tech/programming/parse-large-xml-files-php
     */
    protected function run()
    {
        $validated = collect();

        $xml = $this->startReadFrom();

        // looping through elements
        while ($xml->name == $this->node) {
            $input = $this->extractFieldsFromNode($xml);

            $valid = $this->validateAndLogError($input);

            if ($valid) {
                $validated->push($valid);
            }

            // save the specified number of records at a time
            if ($validated->count() >= $this->max_upsert) {
                $this->save($validated);
                $validated = collect();
            }

            // moving pointer
            $xml->next($this->node);
        }

        $xml->close();

        // save the rest of the records
        $this->save($validated);

        return $this;
    }

    /**
     * Returns valid inputs according to import rules.
     *
     * In case of validation failure, it returns null and logs the failures.
     *
     * @param array<string, string> $inputs assoc array
     *
     * @return array<string, string>|null assoc array
     */
    private function validateAndLogError(array $inputs)
    {
        $validator = Validator::make($inputs, $this->rules);

        if ($validator->fails()) {
            $this->log(
                'warning',
                __('Validation failed'),
                [
                    'input' => $inputs,
                    'error_bag' => $validator->getMessageBag()->toArray(),
                ]
            );

            return null;
        }

        return $validator->validated();
    }

    /**
     * Logs with an arbitrary level.
     *
     * The message MUST be a string or object implementing __toString().
     *
     * The message MAY contain placeholders in the form: {foo} where foo
     * will be replaced by the context data in key "foo".
     *
     * The context array can contain arbitrary data, the only assumption that
     * can be made by implementors is that if an Exception instance is given
     * to produce a stack trace, it MUST be in a key named "exception".
     *
     * @param string                    $level   nível do log
     * @param string|\Stringable        $message sobre o ocorrido
     * @param array<string, mixed>|null $context dados de contexto
     *
     * @return void
     *
     * @see https://www.php-fig.org/psr/psr-3/
     * @see https://www.php.net/manual/en/function.array-merge.php
     */
    private function log(string $level, string|\Stringable $message, ?array $context)
    {
        Log::log(
            level: $level,
            message: $message,
            context: $context + [
                'file_path' => $this->file_path,
                'node' => $this->node,
                'rules' => $this->rules,
                'max_upsert' => $this->max_upsert,
                'unique' => $this->unique,
                'fields_to_update' => $this->fields_to_update,
            ]
        );
    }
}
