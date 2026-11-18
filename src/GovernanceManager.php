<?php

namespace JeffersonGoncalves\QuebecGovernance;

use JeffersonGoncalves\Quebec\Quebec;
use JeffersonGoncalves\QuebecGovernance\Models\ProcessingActivity;

class GovernanceManager
{
    public function __construct(protected Quebec $quebec) {}

    /**
     * The business, the person in charge, the governance policies, and each processing activity with
     * the declared personal information of the models it processes.
     *
     * @return array{business: array<string, mixed>, person_in_charge: array{name: string|null, title: string|null, email: string|null, phone: string|null}, policies: list<array{name: string, url?: string}>, activities: list<array<string, mixed>>}
     */
    public function export(): array
    {
        $inventory = collect($this->quebec->inventory())->groupBy('model');

        return [
            'business' => (array) config('quebec-governance.business', []),
            'person_in_charge' => $this->personInCharge(),
            'policies' => array_values((array) config('quebec-governance.policies', [])),
            'activities' => ProcessingActivity::query()->orderBy('name')->get()
                ->map(fn (ProcessingActivity $activity): array => [
                    'name' => $activity->name,
                    'purpose' => $activity->purpose,
                    'persons' => $activity->persons ?? [],
                    'data_categories' => $activity->data_categories ?? [],
                    'personal_data' => collect($activity->models ?? [])
                        ->flatMap(fn (string $model) => $inventory->get($model, collect())->map(fn (array $row): array => [
                            'table' => $row['table'],
                            'column' => $row['column'],
                            'category' => $row['category'],
                            'retention' => $row['retention'],
                            'outside_quebec' => $row['outside_quebec'],
                            'profiling' => $row['profiling'],
                        ]))
                        ->values()
                        ->all(),
                    'recipients' => $activity->recipients ?? [],
                    'outside_quebec' => $activity->outside_quebec ?? [],
                    'retention' => $activity->retention,
                    'security_measures' => $activity->security_measures,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{name: string|null, title: string|null, email: string|null, phone: string|null}
     */
    public function personInCharge(): array
    {
        $person = (array) config('quebec-governance.person_in_charge', []);

        return [
            'name' => $person['name'] ?? null,
            'title' => $person['title'] ?? null,
            'email' => $person['email'] ?? null,
            'phone' => $person['phone'] ?? null,
        ];
    }

    /**
     * Gaps against the governance duties, as human-readable lines.
     *
     * @return list<string>
     */
    public function gaps(): array
    {
        $export = $this->export();
        $gaps = [];

        if ($export['person_in_charge']['title'] === null || $export['person_in_charge']['email'] === null) {
            $gaps[] = 'Set the title and contact of the person in charge (s. 3.1): quebec-governance.person_in_charge.';
        }

        if ($export['policies'] === []) {
            $gaps[] = 'Publish your governance policies (s. 3.2): quebec-governance.policies.';
        }

        foreach ($export['activities'] as $activity) {
            foreach ($activity['outside_quebec'] as $place) {
                if (! ($place['pia'] ?? false)) {
                    $gaps[] = "{$activity['name']}: assess privacy factors before communicating to {$place['place']} (s. 17).";
                }
            }
        }

        return $gaps;
    }
}
