<?php

namespace JeffersonGoncalves\QuebecGovernance;

use JeffersonGoncalves\QuebecGovernance\Commands\ActivitiesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QuebecGovernanceServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('quebec-governance')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations(['create_quebec_processing_activities_table'])
            ->hasCommand(ActivitiesCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(GovernanceManager::class);
    }
}
