<?php
namespace Blue\Framework\Api\Facade;

use Illuminate\Support\Facades\Facade;

class FrameworkApi extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'blue.framework-api.client';
    }

}