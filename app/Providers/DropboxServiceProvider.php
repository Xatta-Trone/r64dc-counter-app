<?php

namespace App\Providers;

use Spatie\Dropbox\Client;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Illuminate\Filesystem\FilesystemAdapter;

class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('dropbox', function ($app, $config) {

            $client = new Client(config('filesystems.disks.dropbox.authorization_token'));
            $adapter = new DropboxAdapter($client);

            $filesystem = new Filesystem($adapter, ['case_sensitive' => false]);

            return new FilesystemAdapter($filesystem, $adapter);
        });
    }
}
