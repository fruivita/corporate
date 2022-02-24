<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/8.x/mocking
 */

use FruiVita\Corporate\Exceptions\FileNotReadableException;
use FruiVita\Corporate\Exceptions\UnsupportedFileTypeException;
use FruiVita\Corporate\Facades\Corporate;
use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\Person;
use Illuminate\Support\Facades\Log;

test('lança exceção ao executar a importação com arquivo inválido', function ($file_name) {
    expect(
        fn () => Corporate::import($file_name)
    )->toThrow(FileNotReadableException::class);
})->with([
    'inexistente.xml', // inexistente
    '',                // falso boleano
]);

test('lança exceção ao executar a importação com arquivo de mime type não suportado', function () {
    $file_name = 'corporate.txt';
    $this->file_system->put($file_name, 'dumb content');
    $path = $this->file_system->path($file_name);

    expect(
        fn () => Corporate::import($path)
    )->toThrow(UnsupportedFileTypeException::class, 'XML');
});

test('usa o maxupsert default se inválido e cria apenas os logs de validação se o package for configurado para não logar', function () {
    config(['corporate.maxupsert' => -1]); // invalido, pois menor igual a zero
    config(['corporate.logging' => false]);

    $infos
        = 0  // início da importação
        + 0; // fim da importação

    $warnings
        = 6   // cargos inválidos
        + 6   // funções inválidas
        + 18  // lotações inválidas
        + 13; // fim da importação

    Log::shouldReceive('log')
        ->times($infos)
        ->withArgs(
            function ($level) {
                return $level === 'info';
            }
        );

    Log::shouldReceive('log')
        ->times($warnings)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    Corporate::import($this->file_path);

    expect(Occupation::count())->toBe(3)
    ->and(Duty::count())->toBe(3)
    ->and(Department::count())->toBe(5)
    ->and(Person::count())->toBe(5);
});

test('importa a estrutura corporativa completa e cria todos os logs', function () {
    $infos
        = 1  // início da importação
        + 1; // fim da importação

    $warnings
        = 6   // cargos inválidos
        + 6   // funções inválidas
        + 18  // lotações inválidas
        + 13; // fim da importação

    Log::shouldReceive('log')
        ->times($infos)
        ->withArgs(
            function ($level) {
                return $level === 'info';
            }
        );

    Log::shouldReceive('log')
        ->times($warnings)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    Corporate::import($this->file_path);

    expect(Occupation::count())->toBe(3)
    ->and(Duty::count())->toBe(3)
    ->and(Department::count())->toBe(5)
    ->and(Person::count())->toBe(5);
});
