<?php

namespace App\Web3\Storage;

use App\Web3\Storage\APIClient;
use Illuminate\Support\ServiceProvider;

class APIClientServiceProvider extends ServiceProvider
{

    public $singletons = [
        APIClient::class => APIClient::class
    ];
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
