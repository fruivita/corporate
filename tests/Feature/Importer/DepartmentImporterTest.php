<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Importer\DepartmentImporter;
use FruiVita\Corporate\Models\Department;
use Illuminate\Support\Facades\Log;

test('make retorna o objeto da classe', function () {
    expect(DepartmentImporter::make())->toBeInstanceOf(DepartmentImporter::class);
});

test('consegue importar as lotações do arquivo corporativo e cria os autorelacionamentos', function () {
    // forçar a execução de duas queries em pontos distintos e testá-las
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

test('cria os logs para as lotações inválidos', function () {
    Log::shouldReceive('log')
        ->times(18)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    DepartmentImporter::make()->import($this->file_path);

    expect(Department::count())->toBe(5);
});
