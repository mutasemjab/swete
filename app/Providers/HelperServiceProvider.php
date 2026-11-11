<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNClient;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNRegion;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Load currency helper
        require_once app_path('Helpers/General.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registers the "bunnycdn" filesystem disk (see config/filesystems.php) — every file
        // upload in the app goes through this, via App\Support\FileStorage.
        Storage::extend('bunnycdn', function ($app, $config) {
            $adapter = new BunnyCDNAdapter(
                new BunnyCDNClient(
                    $config['storage_zone'],
                    $config['api_key'],
                    $config['region'] ?? BunnyCDNRegion::DEFAULT
                ),
                $config['pull_zone'] ?? ''
            );

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
