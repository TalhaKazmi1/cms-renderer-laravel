<?php

namespace TalhaKazmi\CmsRenderer\Facades;

use Illuminate\Support\Facades\Facade;

class CmsRenderer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cms-renderer';
    }
}
