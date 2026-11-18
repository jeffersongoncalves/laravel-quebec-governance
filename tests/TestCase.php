<?php

namespace JeffersonGoncalves\QuebecGovernance\Tests;

use JeffersonGoncalves\QuebecGovernance\QuebecGovernanceServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            QuebecGovernanceServiceProvider::class,
        ];
    }
}
