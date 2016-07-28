<?php

namespace Ibonly\EtextMail\Facades;

use Illuminate\Support\Facades\Facade;

class EtextMailFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-etextmail';
    }
}