<?php

namespace JeffersonGoncalves\QuebecGovernance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One processing activity of the business: what personal information it collects, why, who receives it,
 * where it goes and how long it is kept.
 *
 * @property string $name
 * @property string $purpose purposes are set before collection and told to the person (s. 8)
 * @property list<string>|null $persons e.g. customers, employees, visitors
 * @property list<string>|null $data_categories free text, beside the declared columns of $models
 * @property list<class-string>|null $models
 * @property list<array{name: string, role?: string}>|null $recipients role: service_provider, third_party, public_body
 * @property list<array{place: string, pia?: bool}>|null $outside_quebec communication outside Quebec, after a privacy impact assessment (s. 17)
 * @property string|null $retention
 * @property string|null $security_measures reasonable given the sensitivity (s. 10)
 */
class ProcessingActivity extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('quebec-governance.table', 'quebec_processing_activities');
    }

    protected function casts(): array
    {
        return [
            'persons' => 'array',
            'data_categories' => 'array',
            'models' => 'array',
            'recipients' => 'array',
            'outside_quebec' => 'array',
        ];
    }
}
