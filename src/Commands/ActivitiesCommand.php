<?php

namespace JeffersonGoncalves\QuebecGovernance\Commands;

use Illuminate\Console\Command;
use JeffersonGoncalves\QuebecGovernance\GovernanceManager;

class ActivitiesCommand extends Command
{
    protected $signature = 'quebec:governance {--json : Print the full export as JSON}';

    protected $description = 'Show the processing activities and the gaps against the Quebec Law 25 governance duties';

    public function handle(GovernanceManager $governance): int
    {
        $export = $governance->export();

        if ($this->option('json')) {
            $this->line((string) json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->table(
            ['Activity', 'Personal information', 'Recipients', 'Outside Quebec', 'Retention'],
            array_map(fn (array $activity): array => [
                $activity['name'],
                implode(', ', array_map(fn (array $row): string => "{$row['table']}.{$row['column']}", $activity['personal_data'])) ?: '-',
                implode(', ', array_column($activity['recipients'], 'name')) ?: '-',
                implode(', ', array_column($activity['outside_quebec'], 'place')) ?: '-',
                $activity['retention'] ?? '-',
            ], $export['activities']),
        );

        foreach ($governance->gaps() as $gap) {
            $this->warn($gap);
        }

        return self::SUCCESS;
    }
}
