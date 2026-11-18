<?php

namespace JeffersonGoncalves\QuebecGovernance\Tests;

use JeffersonGoncalves\PestPluginDatabaseMigrations\LoadsPackageMigrations;
use JeffersonGoncalves\Quebec\QuebecServiceProvider;
use JeffersonGoncalves\QuebecGovernance\QuebecGovernanceServiceProvider;
use JeffersonGoncalves\QuebecGovernance\Tests\Fixtures\Customer;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LoadsPackageMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            QuebecServiceProvider::class,
            QuebecGovernanceServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('quebec.models', [Customer::class]);
        $app['config']->set('quebec-governance.business.name', 'Acme Inc.');
        $app['config']->set('quebec-governance.person_in_charge', ['name' => 'Marie Tremblay', 'title' => 'Privacy Officer', 'email' => 'privacy@acme.test', 'phone' => '+1 514 555 0100']);
        $app['config']->set('quebec-governance.policies', [['name' => 'Retention and destruction', 'url' => '/policies/retention']]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadPackageMigrations(QuebecGovernanceServiceProvider::class);
    }
}
