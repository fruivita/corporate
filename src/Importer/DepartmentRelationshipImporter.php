<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Models\Department;
use Illuminate\Support\Collection;

/**
 * Faz o cadastramento do auto relacionamento da lotação.
 *
 * Deve-se primeiro cadastrar a lotação para depois acionar essa classe para a
 * criar o autorelacionamento.
 * O relacionamento é criado após o cadastro principal da lotação para evitar
 * falhas ao tentar criar de maneira concomitante.
 * Exemplos de falhas que são evitadas ao se fazer o relacionamento após, e não
 * junto, do cadastro da lotação:
 * - lotação pai inexistente, pois o id informado para o pai não existe e não
 * existirá nunca (talvez uma lotação que foi desativada, mas ainda está sendo
 * gerada no arquivo corporativo);
 * - lotação pai inexistente, pois na sequência da leitura do arquivo XML, a
 * lotação pai encontra-se após a lotação que está sendo cadastrada;
 * - lotação pai inexistente, devido a falha em algum atributo que a impediu de
 * ser persistida.
 */
final class DepartmentRelationshipImporter extends BaseImporter
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'id' => ['required', 'integer', 'gte:1'],
        'name' => ['required', 'string',  'max:255'],
        'acronym' => ['required', 'string',  'max:50'],
        'parent_department' => ['nullable', 'integer', 'exists:departments,id'],
    ];

    /**
     * {@inheritdoc}
     */
    protected $node = 'lotacao';

    /**
     * {@inheritdoc}
     */
    protected $unique = ['id'];

    /**
     * {@inheritdoc}
     */
    protected $fields_to_update = ['parent_department'];

    /**
     * Create new class instance.
     */
    public static function make()
    {
        return new static();
    }

    /**
     * {@inheritdoc}
     */
    protected function extractFieldsFromNode(\XMLReader $node)
    {
        return [
            'id' => $node->getAttribute('id') ?: null,
            'name' => $node->getAttribute('nome') ?: null,
            'acronym' => $node->getAttribute('sigla') ?: null,
            'parent_department' => $node->getAttribute('idPai') ?: null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function save(Collection $validated)
    {
        Department::upsert(
            $validated->toArray(),
            $this->unique,
            $this->fields_to_update
        );
    }
}
