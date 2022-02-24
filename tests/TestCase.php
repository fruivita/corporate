<?php

namespace FruiVita\Corporate\Tests;

use FruiVita\Corporate\CorporateServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            // @phpstan-ignore-next-line
            fn ($modelName) => 'FruiVita\\Corporate\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            CorporateServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app): void
    {
        Schema::dropAllTables();

        $files = glob(__DIR__ . '/../database/migrations/*.php.stub', GLOB_NOSORT);

        foreach ($files as $file) {
            $table = include $file;

            $table->up();
        }
    }
}
