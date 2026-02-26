@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'type' => 'button',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-7 py-3 text-base',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-primary text-white hover:bg-primary-dark',
        'outline' => 'bg-transparent text-primary border border-primary hover:bg-primary hover:text-white',
        'danger' => 'bg-secondary-red text-white hover:opacity-80',
        'success' => 'bg-secondary-green text-white hover:opacity-80',
    };

    $classes = "$baseClasses $sizeClasses $variantClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->class([$classes]) }}>
        {{ $slot }}
    </button>
@endif
