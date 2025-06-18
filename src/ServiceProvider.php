<?php

namespace UploadX;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use UploadX\Commands\Install;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        // Optional: keep this to allow manual publish
        $this->publishes([
            __DIR__ . '/../config/uploadx.php' => config_path('uploadx.php'),
        ], 'uploadx-config');
    }

    public function register(): void
    {
        $this->commands([
            Install::class,
        ]);
    }
}
