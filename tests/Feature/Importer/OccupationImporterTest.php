<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Importer\OccupationImporter;
use FruiVita\Corporate\Models\Occupation;
use Illuminate\Support\Facades\Log;

// Failure
test('creates the logs for invalid occupations', function () {
    Log::spy();

    OccupationImporter::make()->import($this->file_path);

    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'warning' && $message === __('Validation failed'))
    ->times(6);

    expect(Occupation::count())->toBe(3);
});

// Happy path
test('make returns the class object', function () {
    expect(OccupationImporter::make())->toBeInstanceOf(OccupationImporter::class);
});

test('import occupations from the corporate file', function () {
    // force the execution of two queries at different points and test them
    config(['corporate.maxupsert' => 2]);

    OccupationImporter::make()->import($this->file_path);

    $occupations = Occupation::get();

    expect($occupations)->toHaveCount(3)
    ->and($occupations->pluck('name'))->toMatchArray(['Cargo 1', 'Cargo 2', 'Cargo 3']);
});
