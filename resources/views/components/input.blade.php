@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'error' => null,
    'success' => false,
])

@php
    $fieldName = $name ?? $attributes->get('wire:model') ?? $attributes->get('wire:model.live') ?? $attributes->get('wire:model.live.blur');
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
        @if($slot->isNotEmpty())
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-primary-grey">
                {{ $slot }}
            </div>
        @endif

        <input
            type="{{ $type }}"
            @if($fieldName) id="{{ $fieldName }}" name="{{ $fieldName }}" @endif
            {{ $attributes->class([
                'w-full border-b-2 bg-transparent py-2 text-sm text-primary-dark placeholder-primary-grey/50 outline-none transition-colors',
                $borderClass,
                'pl-9' => $slot->isNotEmpty(),
            ]) }}
        />

        @if($hasError)
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="size-4 text-secondary-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
                    <circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/>
                </svg>
            </div>
        @elseif($success)
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="size-4 text-secondary-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        @endif
    </div>

    @if($hasError && $errorKey)
        @error($errorKey)
            <p class="mt-1 text-xs text-secondary-red">{{ $message }}</p>
        @enderror
    @elseif($error)
        <p class="mt-1 text-xs text-secondary-red">{{ $error }}</p>
    @endif
</div>
