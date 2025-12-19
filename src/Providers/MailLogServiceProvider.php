<?php

namespace Rboschin\LaravelMailLog\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Rboschin\LaravelMailLog\Logging\MailLogHandler as PackageMailLogHandler;
use Rboschin\LaravelMailLog\Console\Commands\MailLogStatus;
use Rboschin\LaravelMailLog\Console\Commands\MailLogSendTest;

class MailLogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/mail-log.php', 'mail-log');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laravel-mail-log');
    }

    public function boot()
    {
        if (!config('mail-log.enabled')) {
            return;
        }

        $envs = config('mail-log.environments', []);
        if (!empty($envs) && is_array($envs) && !in_array(app()->environment(), $envs, true)) {
            return;
        }

        if (!config('mail-log.auto_register')) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../../config/mail-log.php' => config_path('mail-log.php'),
        ], 'mail-log-config');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/laravel-mail-log'),
        ], 'mail-log-views');

        try {
            $handler = new PackageMailLogHandler();
            $monolog = Log::getLogger();
            if ($monolog && method_exists($monolog, 'pushHandler')) {
                $monolog->pushHandler($handler);
            }
            // Register console commands
            if ($this->app->runningInConsole()) {
                $this->commands([
                    MailLogStatus::class,
                    MailLogSendTest::class,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('MailLogServiceProvider failed to register handler: ' . $e->getMessage());
        }
    }
}
