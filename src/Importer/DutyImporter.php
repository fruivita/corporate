<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Models\Duty;
use Illuminate\Support\Collection;

final class DutyImporter extends BaseImporter
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'id' => ['required', 'integer', 'gte:1'],
        'name' => ['required', 'string',  'max:255'],
    ];

    /**
     * {@inheritdoc}
     */
    protected $node = 'funcao';

    /**
     * {@inheritdoc}
     */
    protected $unique = ['id'];

    /**
     * {@inheritdoc}
     */
    protected $fields_to_update = ['name'];

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
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function save(Collection $validated): void
    {
        Duty::upsert(
            $validated->toArray(),
            $this->unique,
            $this->fields_to_update
        );
    }
}
