<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/8.x/mocking
 */

use FruiVita\Corporate\Importer\DutyImporter;
use FruiVita\Corporate\Models\Duty;
use Illuminate\Support\Facades\Log;

test('make retorna o objeto da classe', function () {
    expect(DutyImporter::make())->toBeInstanceOf(DutyImporter::class);
});

test('consegue importar as duties (funções) do arquivo corporativo', function () {
    // forçar a execução de duas queries em pontos distintos e testá-las
    config(['corporate.maxupsert' => 2]);

    DutyImporter::make()->import($this->file_path);

    $duties = Duty::get();

    expect($duties)->toHaveCount(3)
    ->and($duties->pluck('name'))->toMatchArray(['Função 1', 'Função 2', 'Função 3']);
});

test('cria os logs para as duties (funções) inválidas', function () {
    Log::shouldReceive('log')
        ->times(6)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    DutyImporter::make()->import($this->file_path);

    expect(Duty::count())->toBe(3);
});
