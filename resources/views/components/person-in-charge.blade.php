@props(['label' => null])
@php
    $person = app(\JeffersonGoncalves\QuebecGovernance\GovernanceManager::class)->personInCharge();
@endphp
@if ($person['title'] || $person['email'])
    <address {{ $attributes->merge(['class' => 'quebec-person-in-charge']) }}>
        {{ $label ?? __('quebec-governance::policy.person_in_charge') }}:
        @if ($person['name'])<span>{{ $person['name'] }}</span>@endif
        @if ($person['title'])<span>{{ $person['name'] ? ', ' : '' }}{{ $person['title'] }}</span>@endif
        @if ($person['email']) &middot; <a href="mailto:{{ $person['email'] }}">{{ $person['email'] }}</a>@endif
        @if ($person['phone']) &middot; <a href="tel:{{ preg_replace('/[^0-9+]/', '', $person['phone']) }}">{{ $person['phone'] }}</a>@endif
    </address>
@endif
