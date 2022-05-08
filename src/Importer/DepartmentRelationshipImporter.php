<?php

namespace FruiVita\Corporate\Importer;

use FruiVita\Corporate\Models\Department;
use Illuminate\Support\Collection;

/**
 * Creates the department's self-relationship.
 *
 * You must first create the department and then call this class to create the
 * self-relationship.
 * The relationship is created after the parent department to avoid failures
 * when trying to create it concurrently.
 * Examples of failures that are avoided by creating the relationship after,
 * and not together with, the creation of the parent department:
 * - non-existent parent department, as the id informed for the parent does not
 * exist and will never exist (maybe the department was deactivated, but is
 * still being generated in the corporate file);
 * - non-existent parent department, because in the order of reading the XML
 * file, the parent department is after the child department being created;
 * - non-existent parent department, due to a failure in some attribute that
 * prevented it from being persisted.
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
