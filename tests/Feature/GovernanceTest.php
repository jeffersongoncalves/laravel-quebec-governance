<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use JeffersonGoncalves\QuebecGovernance\Facades\QuebecGovernance;
use JeffersonGoncalves\QuebecGovernance\Models\ProcessingActivity;
use JeffersonGoncalves\QuebecGovernance\Tests\Fixtures\Customer;

function billing(array $outside = [['place' => 'United States', 'pia' => true]]): ProcessingActivity
{
    return ProcessingActivity::create([
        'name' => 'Billing',
        'purpose' => 'Issue invoices',
        'persons' => ['customers'],
        'models' => [Customer::class],
        'recipients' => [['name' => 'Stripe', 'role' => 'service_provider']],
        'outside_quebec' => $outside,
        'retention' => '6 years',
    ]);
}

it('exports the activities joined with the declared personal information', function () {
    billing();

    $export = QuebecGovernance::export();

    expect($export['business']['name'])->toBe('Acme Inc.')
        ->and($export['person_in_charge']['title'])->toBe('Privacy Officer')
        ->and($export['activities'][0])
        ->name->toBe('Billing')
        ->personal_data->toBe([
            ['table' => 'customers', 'column' => 'email', 'category' => 'personal', 'retention' => '5 years', 'outside_quebec' => false, 'profiling' => false],
            ['table' => 'customers', 'column' => 'tax_id', 'category' => 'personal', 'retention' => null, 'outside_quebec' => false, 'profiling' => false],
        ])
        ->outside_quebec->toBe([['place' => 'United States', 'pia' => true]])
        ->and(QuebecGovernance::gaps())->toBe([]);
});

it('reports the governance gaps', function () {
    config()->set('quebec-governance.person_in_charge', []);
    config()->set('quebec-governance.policies', []);
    billing([['place' => 'Ontario']]);

    expect(QuebecGovernance::gaps())->toHaveCount(3)
        ->and(QuebecGovernance::gaps()[2])->toContain('Ontario')->toContain('s. 17');
});

it('prints the activities as a table or JSON, with the gaps', function () {
    billing([['place' => 'Ontario']]);

    Artisan::call('quebec:governance');
    expect(Artisan::output())->toContain('Billing')->toContain('customers.email')->toContain('Stripe')->toContain('s. 17');

    Artisan::call('quebec:governance', ['--json' => true]);
    expect(json_decode(Artisan::output(), true)['activities'][0]['name'])->toBe('Billing');
});

it('renders the privacy policy in English and French', function () {
    billing();

    $html = Blade::render('<x-quebec-governance::privacy-policy rights-url="/privacy/requests" />');

    expect($html)->toContain('Acme Inc. is responsible')
        ->toContain('Billing')
        ->toContain('email, tax id')
        ->toContain('Stripe (service provider)')
        ->toContain('United States')
        ->toContain('6 years')
        ->toContain('de-indexed (s. 28.1)')
        ->toContain('href="/policies/retention"')
        ->toContain('Marie Tremblay')
        ->toContain('href="https://www.cai.gouv.qc.ca"')
        ->not->toContain('profile you')
        ->toContain('href="/privacy/requests"');

    app()->setLocale('fr');

    expect(Blade::render('<x-quebec-governance::privacy-policy />'))
        ->toContain('Politique de confidentialité')
        ->toContain('Stripe (fournisseur de services)')
        ->toContain('Responsable de la protection des renseignements personnels');
});

it('renders the person in charge, or nothing', function () {
    $html = Blade::render('<x-quebec-governance::person-in-charge class="footer" />');

    expect($html)->toContain('Marie Tremblay')
        ->toContain('Privacy Officer')
        ->toContain('mailto:privacy@acme.test')
        ->toContain('tel:+15145550100')
        ->toContain('class="quebec-person-in-charge footer"');

    config()->set('quebec-governance.person_in_charge', []);

    expect(trim(Blade::render('<x-quebec-governance::person-in-charge />')))->toBe('');
});
