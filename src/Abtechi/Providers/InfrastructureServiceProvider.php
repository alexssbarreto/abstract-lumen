<?php

namespace App\Laravel\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(Client::class, function() {
            return new Client([
                'timeout' => Config::get('infrastructure.global.timeout'),
                'connect_timeout' => Config::get('infrastructure.global.connect_timeout', Config::get('infrastructure.global.timeout'))
            ]);
        });
    }
}