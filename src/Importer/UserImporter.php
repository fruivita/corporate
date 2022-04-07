<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Models\User;
use Illuminate\Support\Collection;

final class UserImporter extends BaseImporter
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'username' => ['required', 'string', 'max:20'],
        'name' => ['required', 'string', 'max:255'],
        'department_id' => ['required', 'integer', 'exists:departments,id'],
        'occupation_id' => ['required', 'integer', 'exists:occupations,id'],
        'duty_id' => ['nullable', 'integer', 'exists:duties,id'],
    ];

    /**
     * {@inheritdoc}
     */
    protected $node = 'pessoa';

    /**
     * {@inheritdoc}
     */
    protected $unique = ['username'];

    /**
     * {@inheritdoc}
     */
    protected $fields_to_update = ['name', 'department_id', 'occupation_id', 'duty_id'];

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
            'username' => $node->getAttribute('sigla') ?: null,
            'name' => $node->getAttribute('nome') ?: null,
            'department_id' => $node->getAttribute('lotacao') ?: null,
            'occupation_id' => $node->getAttribute('cargo') ?: null,
            'duty_id' => $node->getAttribute('funcaoConfianca') ?: null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function save(Collection $validated)
    {
        User::upsert(
            $validated->toArray(),
            $this->unique,
            $this->fields_to_update
        );
    }
}
