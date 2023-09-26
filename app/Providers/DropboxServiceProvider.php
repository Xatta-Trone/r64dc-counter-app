<?php

namespace App\Providers;

use Spatie\Dropbox\Client;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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

        // $authorization_token = Cache::remember('dropbox.authorization_token', 14400 - 1, function () {
        //     return $this->getAuthorizationCode();
        // });

        $authorization_token =
            $this->getAuthorizationCode();

        // config(['filesystems.disks.dropbox.authorization_token' => $authorization_token]);


        Storage::extend('dropbox', function ($app, $config) use ($authorization_token) {
            $client = new Client(config('filesystems.disks.dropbox.authorization_token'));
            $adapter = new DropboxAdapter($client);

            $filesystem = new Filesystem($adapter, ['case_sensitive' => false]);

            return new FilesystemAdapter($filesystem, $adapter);
        });
    }

    private function getAuthorizationCode()
    {
        // $response = Http::asForm()->post('https://api.dropbox.com/oauth2/token', [
        //     'grant_type' => 'refresh_token',
        //     'client_id' => env('DROPBOX_APP_KEY'),
        //     'client_secret' => env('DROPBOX_APP_SECRET'),
        //     'refresh_token' => env('DROPBOX_APP_REFRESH_TOKEN'),
        // ]);

        $client_id  = env('DROPBOX_APP_KEY');
        $client_secret  = env('DROPBOX_APP_SECRET');

        $response = Http::asForm()->post('https://' . $client_id . ':' . $client_secret . '@api.dropbox.com/oauth2/token', [
            'grant_type' => 'refresh_token',
            // 'client_id' => env('DROPBOX_APP_KEY'),
            // 'client_secret' => env('DROPBOX_APP_SECRET'),
            'refresh_token' => env('DROPBOX_APP_REFRESH_TOKEN'),
        ]);


        return $response->json()['access_token'];
    }
}
