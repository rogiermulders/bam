<?php
namespace Rogiermulders\Bam;

use Illuminate\Support\ServiceProvider;
use Rogiermulders\Bam\Console\BamConsole;

class BamServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                BamConsole::class,
            ]);
        }
    }
}
