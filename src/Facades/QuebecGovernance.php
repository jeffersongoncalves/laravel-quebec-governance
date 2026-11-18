<?php

namespace JeffersonGoncalves\QuebecGovernance\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\QuebecGovernance\GovernanceManager;

/**
 * @method static array{business: array<string, mixed>, person_in_charge: array{name: string|null, title: string|null, email: string|null, phone: string|null}, policies: list<array{name: string, url?: string}>, activities: list<array<string, mixed>>} export()
 * @method static array{name: string|null, title: string|null, email: string|null, phone: string|null} personInCharge()
 * @method static list<string> gaps()
 *
 * @see GovernanceManager
 */
class QuebecGovernance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GovernanceManager::class;
    }
}
