<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/8.x/mocking
 */

use FruiVita\Corporate\Importer\OccupationImporter;
use FruiVita\Corporate\Models\Occupation;
use Illuminate\Support\Facades\Log;

test('make retorna o objeto da classe', function () {
    expect(OccupationImporter::make())->toBeInstanceOf(OccupationImporter::class);
});

test('consegue importar os cargos do arquivo corporativo', function () {
    // forçar a execução de duas queries em pontos distintos e testá-las
    config(['corporate.maxupsert' => 2]);

    OccupationImporter::make()->import($this->file_path);

    $occupations = Occupation::get();

    expect($occupations)->toHaveCount(3)
    ->and($occupations->pluck('name'))->toMatchArray(['Cargo 1', 'Cargo 2', 'Cargo 3']);
});

test('cria os logs para as cargos inválidas', function () {
    Log::shouldReceive('log')
        ->times(6)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    OccupationImporter::make()->import($this->file_path);

    expect(Occupation::count())->toBe(3);
});
