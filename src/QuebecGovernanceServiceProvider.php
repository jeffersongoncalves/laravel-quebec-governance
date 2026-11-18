<?php

namespace JeffersonGoncalves\QuebecGovernance;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QuebecGovernanceServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-quebec-governance')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
