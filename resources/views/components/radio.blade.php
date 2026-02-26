@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'disabled' => false,
    'error' => false,
    'success' => false,
])

@php
    $fieldName = $name ?? $attributes->get('wire:model') ?? $attributes->get('wire:model.live');
    $hasError = $error || ($fieldName && isset($errors) && $errors->has($fieldName));
    $colorClass = match(true) {
        $hasError => 'text-secondary-red border-secondary-red focus:ring-secondary-red',
        $success => 'text-secondary-green border-secondary-green focus:ring-secondary-green',
        default => 'text-primary border-outline focus:ring-primary',
    };
@endphp

<label class="inline-flex items-center gap-2 {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
    <input
        type="radio"
        @if($fieldName) name="{{ $fieldName }}" @endif
        @if($value) value="{{ $value }}" @endif
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->class([
            'size-4 rounded-full border-2 focus:ring-2 focus:ring-offset-0 transition-colors',
            $colorClass,
        ]) }}
    />
    @if($label)
        <span class="text-sm {{ $hasError ? 'text-secondary-red' : ($success ? 'text-secondary-green' : 'text-primary-dark') }}">
            {{ $label }}
        </span>
    @endif
</label>
