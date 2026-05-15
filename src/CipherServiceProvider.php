<?php

namespace Layman\LaravelCipher;

use Illuminate\Support\ServiceProvider;
use Layman\LaravelCipher\Commands\CipherCommand;
use Layman\LaravelCipher\Services\CipherService;

class CipherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cipher.php', 'cipher');
        // 注册 Facade 对应的服务名称
        $this->app->singleton('cipher', function ($app) {
            return $app->make(CipherService::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        $this->publishes([
            __DIR__.'/../config/cipher.php' => config_path('cipher.php'),
        ], 'cipher');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CipherCommand::class,
            ]);
        }
    }
}
