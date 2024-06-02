<?php

namespace FI\Modules\PendingClients\Providers;

use FI\Modules\PendingClients\Controllers\PendingClientController;
use Illuminate\Support\ServiceProvider;

class ModuleProvider extends ServiceProvider {

	public function register()
	{
        $this->app->bind('PendingClientController', function($app)
        {
            return new PendingClientController(
                $app->make('ClientRepository')
            );
        });
	}

}