<?php

namespace FruiVita\Corporate;

use Illuminate\Support\ServiceProvider;

/**
 * @see https://laravel.com/docs/9.x/packages
 * @see https://laravel.com/docs/9.x/packages#service-providers
 * @see https://laravel.com/docs/9.x/providers
 */
class CorporateServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'corporate');

        $this->app->bind('corporate', function ($app) {
            return new Corporate();
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../lang');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../lang' => lang_path('lang/vendor/corporate'),
            ], 'lang');

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('corporate.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_occupations_table.php.stub' => database_path('migrations/2020_01_01_000000_create_occupations_table.php'),
                __DIR__ . '/../database/migrations/create_duties_table.php.stub' => database_path('migrations/2020_01_01_000000_create_duties_table.php'),
                __DIR__ . '/../database/migrations/create_departments_table.php.stub' => database_path('migrations/2020_01_01_000000_create_departments_table.php'),
                __DIR__ . '/../database/migrations/create_users_table.php.stub' => database_path('migrations/2020_01_01_000000_create_users_table.php'),
            ], 'migrations');
        }
    }
}
