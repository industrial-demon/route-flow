@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'radius' => 'default',
    'disabled' => false,
    'fullWidth' => false,
])

@php
    $variantClasses = match ($variant) {
        'primary' => 'border-transparent bg-primary text-primary-foreground enabled:hover:bg-primary-hover enabled:active:bg-primary-hover focus-visible:outline-primary-content',
        'danger' => 'border-transparent bg-accent text-accent-foreground enabled:hover:bg-accent-hover enabled:active:bg-accent-hover focus-visible:outline-accent-content',
        'secondary' => 'border-transparent bg-foreground text-surface enabled:hover:bg-muted enabled:active:bg-muted focus-visible:outline-foreground',
        'outline' => 'border-border bg-transparent text-foreground enabled:hover:bg-foreground/5 enabled:active:bg-foreground/10 focus-visible:outline-foreground',
        'ghost' => 'border-transparent bg-transparent text-foreground enabled:hover:bg-foreground/5 enabled:active:bg-foreground/10 focus-visible:outline-foreground',
        default => throw new \InvalidArgumentException("Unknown button variant [{$variant}]. Use primary, danger, secondary, outline, or ghost."),
    };

    $sizes = [
        'sm' => 'h-[35px] gap-2 px-4 text-sm',
        'md' => 'h-10 gap-2 px-6 text-sm',
        'lg' => 'h-12 gap-2.5 px-8 text-base',
    ];

    $radii = [
        'default' => 'rounded-button',
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];
@endphp

<button
    type="{{ $type }}"
    @disabled($disabled)
    {{ $attributes->class([
        'inline-flex shrink-0 items-center justify-center whitespace-nowrap border font-medium transition-colors motion-reduce:transition-none',
        'cursor-pointer disabled:cursor-not-allowed disabled:opacity-50',
        'focus-visible:outline-2 focus-visible:outline-offset-2',
        '[&_svg]:size-4 [&_svg]:shrink-0',
        $variantClasses,
        $sizes[$size] ?? $sizes['md'],
        $radii[$radius] ?? $radii['default'],
        'w-full' => $fullWidth,
    ]) }}
>
    {{ $slot }}
</button>
