<?php

namespace Freelabois\LaravelQuickstart\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelQuickstart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-quickstart';
    }
}
