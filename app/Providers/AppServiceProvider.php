<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Providers;

use FI\Support\Directory;
use FI\Support\Migrations;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrap();

        if (request()->segment(1) !== 'setup')
        {
            if (!Schema::hasTable('migrations'))
            {
                // This appears to be a new install, so redirect the client to /setup.
                redirect('setup')->send();
            }

            $migrations = new Migrations();

            if ($migrations->getPendingMigrations(base_path('database/migrations')))
            {
                // It appears there are pending migrations to run, so redirect the client to /setup.
                redirect('setup')->send();
            }
        }

        if (config('proxies.trust_all'))
        {
            request()->setTrustedProxies([request()->getClientIp()]);
        }

        $this->app->view->addLocation(base_path('custom/overrides'));

        $modules = Directory::listDirectories(app_path('Modules'));

        foreach ($modules as $module)
        {
            $routesPath = app_path('Modules/' . $module . '/routes.php');
            $viewsPath  = app_path('Modules/' . $module . '/Views');

            if (file_exists($routesPath))
            {
                require $routesPath;
            }

            if (file_exists($viewsPath))
            {
                $this->app->view->addLocation($viewsPath);
            }
        }

        foreach (File::files(app_path('Helpers')) as $helper)
        {
            require_once $helper;
        }

        $this->app->view->addLocation(base_path('custom/templates'));
        $this->app->view->addLocation(storage_path());

        $this->app->register('FI\Providers\AddonServiceProvider');
        $this->app->register('FI\Providers\ComposerServiceProvider');
        $this->app->register('FI\Providers\ConfigServiceProvider');
        $this->app->register('FI\Providers\DashboardWidgetServiceProvider');
        $this->app->register('FI\Providers\EventServiceProvider');
        $this->app->register(AuthServiceProvider::class);
    }

    public function register()
    {
        $this->app->booting(function ()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('Sortable', 'FI\Traits\Sortable');
        });
    }
}
