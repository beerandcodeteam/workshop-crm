@props([
    'label' => null,
    'name' => null,
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

    <textarea
        @if($fieldName) id="{{ $fieldName }}" name="{{ $fieldName }}" @endif
        {{ $attributes->class([
            'w-full border-b-2 bg-transparent py-2 text-sm text-primary-dark placeholder-primary-grey/50 outline-none transition-colors resize-y min-h-[80px]',
            $borderClass,
        ]) }}
    >{{ $slot }}</textarea>

    @if($hasError && $errorKey)
        @error($errorKey)
            <p class="mt-1 text-xs text-secondary-red">{{ $message }}</p>
        @enderror
    @elseif($error)
        <p class="mt-1 text-xs text-secondary-red">{{ $error }}</p>
    @endif
</div>
