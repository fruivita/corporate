<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Importer\DutyImporter;
use FruiVita\Corporate\Models\Duty;
use Illuminate\Support\Facades\Log;

// Failure
test('creates the logs for invalid duties', function () {
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

// Happy path
test('make returns the class object', function () {
    expect(DutyImporter::make())->toBeInstanceOf(DutyImporter::class);
});

test('import duties from the corporate file', function () {
    // force the execution of two queries at different points and test them
    config(['corporate.maxupsert' => 2]);

    DutyImporter::make()->import($this->file_path);

    $duties = Duty::get();

    expect($duties)->toHaveCount(3)
    ->and($duties->pluck('name'))->toMatchArray(['Função 1', 'Função 2', 'Função 3']);
});
