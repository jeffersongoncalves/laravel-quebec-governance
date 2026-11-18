@props(['rightsUrl' => null])
@php
    $export = app(\JeffersonGoncalves\QuebecGovernance\GovernanceManager::class)->export();
    $authority = (array) config('quebec-governance.authority', []);
    $updatedAt = \JeffersonGoncalves\QuebecGovernance\Models\ProcessingActivity::query()->max('updated_at');
    $label = fn (string $group, ?string $key): ?string => $key === null ? null
        : (\Illuminate\Support\Facades\Lang::has("quebec-governance::policy.{$group}.{$key}") ? __("quebec-governance::policy.{$group}.{$key}") : $key);
    $profiling = collect($export['activities'])->flatMap(fn (array $a) => $a['personal_data'])->contains('profiling', true);
@endphp
<article {{ $attributes->merge(['class' => 'quebec-privacy-policy']) }}>
    <h2>{{ __('quebec-governance::policy.title') }}</h2>
    @if ($updatedAt)
        <p>{{ __('quebec-governance::policy.updated_at', ['date' => \Illuminate\Support\Carbon::parse($updatedAt)->translatedFormat('Y-m-d')]) }}</p>
    @endif

    <section>
        <h3>{{ __('quebec-governance::policy.business') }}</h3>
        @if ($export['business']['name'] ?? null)
            <p>{{ __('quebec-governance::policy.business_text', ['name' => $export['business']['name']]) }}</p>
        @endif
        <ul>
            @if ($export['business']['address'] ?? null)<li>{{ __('quebec-governance::policy.address') }}: {{ $export['business']['address'] }}</li>@endif
            @if ($export['business']['contact'] ?? null)<li>{{ __('quebec-governance::policy.contact') }}: {{ $export['business']['contact'] }}</li>@endif
        </ul>
        <x-quebec-governance::person-in-charge />
        @if ($export['policies'] !== [])
            <p>{{ __('quebec-governance::policy.policies') }}</p>
            <ul>
                @foreach ($export['policies'] as $policy)
                    <li>@if ($policy['url'] ?? null)<a href="{{ $policy['url'] }}">{{ $policy['name'] }}</a>@else{{ $policy['name'] }}@endif</li>
                @endforeach
            </ul>
        @endif
    </section>

    <section>
        <h3>{{ __('quebec-governance::policy.processing') }}</h3>
        @forelse ($export['activities'] as $activity)
            <section>
                <h4>{{ $activity['name'] }}</h4>
                <dl>
                    <dt>{{ __('quebec-governance::policy.purpose') }}</dt>
                    <dd>{{ $activity['purpose'] }}</dd>
                    @if ($activity['data_categories'] !== [] || $activity['personal_data'] !== [])
                        <dt>{{ __('quebec-governance::policy.data') }}</dt>
                        <dd>{{ collect($activity['data_categories'])
                            ->merge(collect($activity['personal_data'])->map(fn (array $row): string => str_replace('_', ' ', $row['column'])
                                .($row['category'] === 'sensitive' ? ' ('.__('quebec-governance::policy.sensitive').')' : '')))
                            ->unique()->implode(', ') }}</dd>
                    @endif
                    @if ($activity['persons'] !== [])
                        <dt>{{ __('quebec-governance::policy.persons') }}</dt>
                        <dd>{{ implode(', ', $activity['persons']) }}</dd>
                    @endif
                    @if ($activity['recipients'] !== [])
                        <dt>{{ __('quebec-governance::policy.recipients') }}</dt>
                        <dd>{{ collect($activity['recipients'])->map(fn (array $r): string => $r['name'].(isset($r['role']) ? ' ('.$label('roles', $r['role']).')' : ''))->implode(', ') }}</dd>
                    @endif
                    @if ($activity['outside_quebec'] !== [])
                        <dt>{{ __('quebec-governance::policy.outside_quebec') }}</dt>
                        <dd>{{ collect($activity['outside_quebec'])->pluck('place')->implode(', ') }}</dd>
                    @endif
                    @if ($activity['retention'])
                        <dt>{{ __('quebec-governance::policy.retention') }}</dt>
                        <dd>{{ $activity['retention'] }}</dd>
                    @endif
                </dl>
            </section>
        @empty
            <p>{{ __('quebec-governance::policy.none') }}</p>
        @endforelse
        @if ($profiling)
            <p>{{ __('quebec-governance::policy.profiling') }}</p>
        @endif
    </section>

    <section>
        <h3>{{ __('quebec-governance::policy.rights') }}</h3>
        <p>{{ __('quebec-governance::policy.rights_intro') }}</p>
        <ul>
            @foreach ((array) __('quebec-governance::policy.rights_list') as $right)
                <li>{{ $right }}</li>
            @endforeach
        </ul>
        @if ($rightsUrl)
            <p><a href="{{ $rightsUrl }}">{{ __('quebec-governance::policy.rights_request') }}</a></p>
        @else
            <p>{{ __('quebec-governance::policy.rights_contact') }}</p>
        @endif
        <p>
            {{ __('quebec-governance::policy.complaint') }}
            @if ($authority['name'] ?? null)
                @if ($authority['url'] ?? null)<a href="{{ $authority['url'] }}">{{ $authority['name'] }}</a>@else{{ $authority['name'] }}@endif.
            @endif
        </p>
    </section>
</article>
