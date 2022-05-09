<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Importer\DepartmentImporter;
use FruiVita\Corporate\Importer\DutyImporter;
use FruiVita\Corporate\Importer\OccupationImporter;
use FruiVita\Corporate\Importer\UserImporter;
use FruiVita\Corporate\Models\User;
use Illuminate\Support\Facades\Log;

// Failure
test('creates the logs for invalid users', function () {
    OccupationImporter::make()->import($this->file_path);
    DutyImporter::make()->import($this->file_path);
    DepartmentImporter::make()->import($this->file_path);

    Log::spy();

    UserImporter::make()->import($this->file_path);

    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'warning' && $message === __('Validation failed'))
    ->times(13);

    expect(User::count())->toBe(5);
});

// Happy path
test('make returns the class object', function () {
    expect(UserImporter::make())->toBeInstanceOf(UserImporter::class);
});

test('import users from the corporate file', function () {
    // force the execution of two queries at different points and test them
    config(['corporate.maxupsert' => 2]);

    OccupationImporter::make()->import($this->file_path);
    DutyImporter::make()->import($this->file_path);
    DepartmentImporter::make()->import($this->file_path);
    UserImporter::make()->import($this->file_path);

    $users = User::get();

    expect($users)->toHaveCount(5)
    ->and($users->pluck('name'))->toMatchArray(['Pessoa 1', 'Pessoa 2', 'Pessoa 3', 'Pessoa 4', 'Pessoa 5']);
});
