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

    $infos
        = 0  // Start of import
        + 0; // End of import

    $warnings
        = 6   // Ocuppation (Cargo) invalid
        + 6   // Duty (função comissionada) invalid
        + 18  // Department (lotação) invalid
        + 13; // User (Pessoa) invalid

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
    ->and(User::count())->toBe(5);
});

test('import the complete corporate structure and create all logs', function () {
    $infos
        = 1  // Start of import
        + 1; // End of import

    $warnings
        = 6   // Ocuppation (Cargo) invalid
        + 6   // Duty (função comissionada) invalid
        + 18  // Department (Lotação) invalid
        + 13; // User (Pessoa) invalid

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
    ->and(User::count())->toBe(5);
});
