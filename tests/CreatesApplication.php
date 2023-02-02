<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
	
	/**
         * The \Illuminate\Foundation\Console\Kernel class, is what the Illuminate\Support\Facades\Artisan facade exposes
         * But you cannot resolve facades via the container, which is of course by nature of that design pattern.
         * The below method allows us retain the speed of sqlite in memory testing.
         * But without having to use the "DatabaseMigrations" trait, which can only rebuild the entire database after every test. 
         */
        $artisan = $app->make(\Illuminate\Foundation\Console\Kernel::class);
        $artisan->call('migrate:fresh');

        return $app;
    }
}
