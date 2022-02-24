<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Models\Department;
use Illuminate\Support\Collection;

final class DepartmentImporter extends BaseImporter
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'id' => ['required', 'integer', 'gte:1'],
        'name' => ['required', 'string',  'max:255'],
        'acronym' => ['required', 'string',  'max:50'],
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
    protected $fields_to_update = ['name', 'acronym'];

    /**
     * Create new class instance.
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * {@inheritdoc}
     */
    protected function extractFieldsFromNode(\XMLReader $node): array
    {
        return [
            'id' => $node->getAttribute('id') ?: null,
            'name' => $node->getAttribute('nome') ?: null,
            'acronym' => $node->getAttribute('sigla') ?: null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function save(Collection $validated): void
    {
        Department::upsert(
            $validated->toArray(),
            $this->unique,
            $this->fields_to_update
        );

        DepartmentRelationshipImporter::make()->import($this->file_path);
    }
}
