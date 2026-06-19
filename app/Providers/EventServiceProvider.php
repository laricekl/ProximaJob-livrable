<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

 protected $listen = [
    'Illuminate\Auth\Events\Login' => [
        \App\Listeners\UpdateLastLoginAt::class,
    ],
    ProfileUpdated::class => [
        ProcessAutoMatching::class,
    ],

];

 
}