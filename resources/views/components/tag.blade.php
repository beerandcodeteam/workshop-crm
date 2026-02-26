@props([
    'variant' => 'primary',
    'removable' => false,
])

@php
    $variantClasses = match($variant) {
        'primary' => 'bg-primary/10 text-primary',
        'yellow' => 'bg-secondary-yellow/10 text-secondary-yellow',
        'green' => 'bg-secondary-green/10 text-secondary-green',
        'red' => 'bg-secondary-red/10 text-secondary-red',
        'purple' => 'bg-secondary-purple/10 text-secondary-purple',
        'outline' => 'bg-transparent text-primary-grey border border-outline',
    };
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium',
    $variantClasses,
]) }}>
    {{ $slot }}

    @if($removable)
        <button type="button" class="ml-0.5 hover:opacity-70 transition-opacity" @if($attributes->has('wire:click')) wire:click="{{ $attributes->get('wire:click') }}" @endif>
            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</span>
