<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Importer\DepartmentImporter;
use FruiVita\Corporate\Importer\DutyImporter;
use FruiVita\Corporate\Importer\OccupationImporter;
use FruiVita\Corporate\Importer\PersonImporter;
use FruiVita\Corporate\Models\Person;
use Illuminate\Support\Facades\Log;

test('make retorna o objeto da classe', function () {
    expect(PersonImporter::make())->toBeInstanceOf(PersonImporter::class);
});

test('consegue importar as pessoas do arquivo corporativo', function () {
    // forçar a execução de duas queries em pontos distintos e testá-las
    config(['corporate.maxupsert' => 2]);

    OccupationImporter::make()->import($this->file_path);
    DutyImporter::make()->import($this->file_path);
    DepartmentImporter::make()->import($this->file_path);
    PersonImporter::make()->import($this->file_path);

    $persons = Person::get();

    expect($persons)->toHaveCount(5)
    ->and($persons->pluck('name'))->toMatchArray(['Pessoa 1', 'Pessoa 2', 'Pessoa 3', 'Pessoa 4', 'Pessoa 5']);
});

test('cria os logs para as pessoas inválidos', function () {
    OccupationImporter::make()->import($this->file_path);
    DutyImporter::make()->import($this->file_path);
    DepartmentImporter::make()->import($this->file_path);

    Log::shouldReceive('log')
        ->times(13)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    PersonImporter::make()->import($this->file_path);

    expect(Person::count())->toBe(5);
});
