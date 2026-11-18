<?php

namespace JeffersonGoncalves\QuebecGovernance\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\QuebecGovernance\QuebecGovernance
 */
class QuebecGovernance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-quebec-governance';
    }
}
