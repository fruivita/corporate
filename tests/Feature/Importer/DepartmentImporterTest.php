<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Importer\DepartmentImporter;
use FruiVita\Corporate\Models\Department;
use Illuminate\Support\Facades\Log;

// Failure
test('creates the logs for invalid departments', function () {
    Log::spy();

    DepartmentImporter::make()->import($this->file_path);

    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'warning' && $message === __('Validation failed'))
    ->times(18);

    expect(Department::count())->toBe(5);
});

// Happy path
test('make returns the class object', function () {
    expect(DepartmentImporter::make())->toBeInstanceOf(DepartmentImporter::class);
});

test('import departments from the corporate file and create self-relationships', function () {
    // force the execution of two queries at different points and test them
    config(['corporate.maxupsert' => 2]);

    DepartmentImporter::make()->import($this->file_path);

    $departments = Department::get();

    expect($departments)->toHaveCount(5)
    ->and($departments->pluck('name'))->toMatchArray(['Lotação 1', 'Lotação 2', 'Lotação 3', 'Lotação 4', 'Lotação 5'])
    ->and($departments->pluck('acronym'))->toMatchArray(['Sigla 1', 'Sigla 2', 'Sigla 3', 'Sigla 4', 'Sigla 5'])
    ->and(Department::has('parentDepartment')->count())->toBe(2)
    ->and(Department::has('childDepartments')->count())->toBe(1)
    ->and(
        Department::with('childDepartments')
            ->find('1')
            ->childDepartments
            ->pluck('name')
    )->toMatchArray(['Lotação 3', 'Lotação 5'])
    ->and(
        Department::with('parentDepartment')
            ->find('1')
            ->name
    )->toBe('Lotação 1');
});
