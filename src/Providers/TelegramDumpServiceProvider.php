<?php

namespace NicolaeSoitu\TelegramDump\Providers;

use Illuminate\Support\ServiceProvider;
use NicolaeSoitu\TelegramDump\Console\Commands\TelegramDumpTestCommand;
use NicolaeSoitu\TelegramDump\Console\Commands\TelegramDumpGetUpdatesCommand;


class TelegramDumpServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      $this->publishes([
        __DIR__.'/../config/telegram-dump.php' => config_path('telegram-dump.php'),
      ]);
      if ($this->app->runningInConsole()) {
        $this->commands([
          TelegramDumpTestCommand::class,
          TelegramDumpGetUpdatesCommand::class
        ]);
      }
    }
}
