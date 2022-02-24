<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Importer\Contracts\IImportable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

abstract class BaseImporter implements IImportable
{
    /**
     * Regras que serão aplicadas a cada campo do nó XML que será importado.
     *
     * @var array<string, mixed[]> assoc array
     */
    protected $rules;

    /**
     * Nome do nó XML que será importado.
     *
     * @var string
     */
    protected $node;

    /**
     * Atributos (campos) para se considerar o objeto único no banco de dados.
     *
     * @var string[]
     */
    protected $unique;

    /**
     * Atributos (campos) que devem ser atualizados no banco de dados se o
     * objeto já estiver persistido.
     *
     * @var string[]
     */
    protected $fields_to_update;

    /**
     * Path completo para o arquivo XML com a estrutura corporativa que será
     * importado.
     *
     * @var string
     */
    protected $file_path;

    /**
     * Quantidade de registros que será inserida/atualizada em uma única query.
     *
     * @var int
     */
    protected $max_upsert;

    /**
     * {@inheritdoc}
     */
    public function import(string $file_path): void
    {
        $this
            ->setFilePath($file_path)
            ->setMaxUpsert()
            ->run();
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
     * Define a quantidade de registros que será inserida/atualizada em uma
     * única query.
     *
     * @return static
     */
    private function setMaxUpsert(): static
    {
        $max = config('corporate.maxupsert');

        $this->max_upsert = (is_int($max) && $max >= 1)
                                ? $max
                                : 500;

        return $this;
    }

    /**
     * Extrai do nó XML os campos de interesse para o objeto.
     *
     * O array conterá os campos de interesse (key), e os respectivos valores
     * (value) extraídos do nó xml informado.
     *
     * Ex.: [
     *     'id' => '10',
     *     'nome' => 'Fábio Cassiano',
     * ]
     *
     * @param \XMLReader $node nó de onde serão extraídos os valores
     *
     * @return array<string, string> array assoc
     */
    abstract protected function extractFieldsFromNode(\XMLReader $node): array;

    /**
     * Faz a persistência dos itens validados.
     *
     * @param \Illuminate\Support\Collection $validated
     *
     * @return void
     */
    abstract protected function save(Collection $validated): void;

    /**
     * Posiciona o XMLReader no primeiro nó XML que deverá ser trabalhado.
     *
     * Ex: se o desejo for ler os **cargos**, o arquivo XML será lido pelo
     * **XMLReader** retornando o ponteiro apontando para o primeiro nó com o
     * nome **cargo** para que eles sejam processados.
     *
     * @return \XMLReader
     *
     * @see https://drib.tech/programming/parse-large-xml-files-php
     */
    protected function startReadFrom(): \XMLReader
    {
        $xml = new \XMLReader();
        $xml->open($this->file_path);

        // finding first primary element to work with
        while ($xml->read() && $xml->name != $this->node) {
        }

        return $xml;
    }

    /**
     * Executa a importação propriamente dita.
     *
     * A execução é feita por meio dos seguintes passos:
     * - Ler o arquivo;
     * - Extrair os dados do nó xml de interesse;
     * - Validar os dados extraídos e, se preciso, logar as inconsistências;
     * - Acionar o método responsável pela persistência.
     *
     * @return static
     *
     * @see https://drib.tech/programming/parse-large-xml-files-php
     */
    protected function run(): static
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

            // salva a quantidade determinada de registros por vez
            if ($validated->count() >= $this->max_upsert) {
                $this->save($validated);
                $validated = collect();
            }

            // moving pointer
            $xml->next($this->node);
        }

        $xml->close();

        // salva o saldo dos registros
        $this->save($validated);

        return $this;
    }

    /**
     * Retorna os inputs válidos de acordo com as rules de importação.
     *
     * Em caso de falha de validação, retorna null e loga as falhas.
     *
     * @param array<string, string> $inputs assoc array
     *
     * @return array<string, string>|null assoc array
     */
    private function validateAndLogError(array $inputs): ?array
    {
        $validator = Validator::make($inputs, $this->rules);

        if ($validator->fails()) {
            $this->log(
                'warning',
                __('corporate::Validation failed!'),
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
    private function log(string $level, string|\Stringable $message, ?array $context): void
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
