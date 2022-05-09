<?php

/**
 * @see https://pestphp.com/docs/
 * @see https://laravel.com/docs/9.x/mocking
 */

use FruiVita\Corporate\Exceptions\FileNotReadableException;
use FruiVita\Corporate\Exceptions\UnsupportedFileTypeException;
use FruiVita\Corporate\Facades\Corporate;
use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\User;
use Illuminate\Support\Facades\Log;

// Exceptions
test('throws exception when running import with invalid file', function ($filename) {
    expect(
        fn () => Corporate::import($filename)
    )->toThrow(FileNotReadableException::class);
})->with([
    'foo.xml',
    '',
]);

test('throws exception when running import with unsupported mime type file', function () {
    $filename = 'corporate.txt';
    $this->file_system->put($filename, 'dumb content');
    $path = $this->file_system->path($filename);

    expect(
        fn () => Corporate::import($path)
    )->toThrow(UnsupportedFileTypeException::class, 'XML');
});

// Happy path
test('if invalid, use the default maxupsert. Also, creates the minimum logs (validation), even if configured to not log.', function () {
    config(['corporate.maxupsert' => -1]); // invalid. less then or equal to zero
    config(['corporate.logging' => false]);

    $warnings
        = 6   // Ocuppation (Cargo) invalid
        + 6   // Duty (função comissionada) invalid
        + 18  // Department (lotação) invalid
        + 13; // User (Pessoa) invalid

    Log::spy();

    Corporate::import($this->file_path);

    Log::shouldNotHaveReceived(
        'log',
        fn ($level, $message) => $level === 'info' && $message === __('Start of corporate structure import')
    );
    Log::shouldNotHaveReceived(
        'log',
        fn ($level, $message) => $level === 'info' && $message === __('End of corporate structure import')
    );
    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'warning' && $message === __('Validation failed'))
    ->times($warnings);

    expect(Occupation::count())->toBe(3)
    ->and(Duty::count())->toBe(3)
    ->and(Department::count())->toBe(5)
    ->and(User::count())->toBe(5);
});

test('import the complete corporate structure and create all logs', function () {
    $warnings
        = 6   // Ocuppation (Cargo) invalid
        + 6   // Duty (função comissionada) invalid
        + 18  // Department (Lotação) invalid
        + 13; // User (Pessoa) invalid

    Log::spy();

    Corporate::import($this->file_path);

    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'info' && $message === __('Start of corporate structure import'))
    ->once();
    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'warning' && $message === __('Validation failed'))
    ->times($warnings);
    Log::shouldHaveReceived('log')
    ->withArgs(fn ($level, $message) => $level === 'info' && $message === __('End of corporate structure import'))
    ->once();

    expect(Occupation::count())->toBe(3)
    ->and(Duty::count())->toBe(3)
    ->and(Department::count())->toBe(5)
    ->and(User::count())->toBe(5);
});
