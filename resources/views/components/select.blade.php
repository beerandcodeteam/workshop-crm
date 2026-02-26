@props([
    'label' => null,
    'name' => null,
    'placeholder' => null,
    'error' => null,
    'success' => false,
])

@php
    $fieldName = $name ?? $attributes->get('wire:model') ?? $attributes->get('wire:model.live');
    $errorKey = $fieldName;
    $hasError = $error || ($errorKey && isset($errors) && $errors->has($errorKey));
    $borderClass = match(true) {
        $hasError => 'border-secondary-red',
        $success => 'border-secondary-green',
        default => 'border-outline focus:border-primary',
    };
@endphp

<div>
    @if($label)
        <label for="{{ $fieldName }}" class="block text-xs font-medium text-primary-grey mb-1">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <select
            @if($fieldName) id="{{ $fieldName }}" name="{{ $fieldName }}" @endif
            {{ $attributes->class([
                'w-full appearance-none border-b-2 bg-transparent py-2 pr-8 text-sm text-primary-dark outline-none transition-colors cursor-pointer',
                $borderClass,
            ]) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>

        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="size-4 text-primary-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    @if($hasError && $errorKey)
        @error($errorKey)
            <p class="mt-1 text-xs text-secondary-red">{{ $message }}</p>
        @enderror
    @elseif($error)
        <p class="mt-1 text-xs text-secondary-red">{{ $error }}</p>
    @endif
</div>
