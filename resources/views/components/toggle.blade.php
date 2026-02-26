@props([
    'label' => null,
    'name' => null,
    'disabled' => false,
    'error' => false,
    'success' => false,
])

@php
    $fieldName = $name ?? $attributes->get('wire:model') ?? $attributes->get('wire:model.live');
    $hasError = $error || ($fieldName && $errors->has($fieldName));
    $trackColor = match(true) {
        $hasError => 'peer-checked:bg-secondary-red bg-secondary-red/30',
        $success => 'peer-checked:bg-secondary-green bg-secondary-green/30',
        default => 'peer-checked:bg-primary bg-primary-grey/30',
    };
@endphp

<label class="inline-flex items-center gap-2 {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
    <div class="relative">
        <input
            type="checkbox"
            @if($fieldName) id="{{ $fieldName }}" name="{{ $fieldName }}" @endif
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->class(['sr-only peer']) }}
        />
        <div class="w-10 h-5 rounded-full transition-colors {{ $trackColor }}"></div>
        <div class="absolute left-0.5 top-0.5 size-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
    </div>
    @if($label)
        <span class="text-sm {{ $hasError ? 'text-secondary-red' : ($success ? 'text-secondary-green' : 'text-primary-dark') }}">
            {{ $label }}
        </span>
    @endif
</label>
