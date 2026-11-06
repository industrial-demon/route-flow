@props([
    'type' => 'button',
    'variant' => 'solid',
    'color' => 'primary',
    'size' => 'md',
    'radius' => 'default',
    'outline' => false,
    'disabled' => false,
    'fullWidth' => false,
])

@php
    $variant = $outline ? 'outline' : $variant;

    $colors = [
        'primary' => '[--button-color:var(--color-primary)] [--button-hover:var(--color-primary-hover)] [--button-content:var(--color-primary-content)] [--button-foreground:var(--color-primary-foreground)]',
        'accent' => '[--button-color:var(--color-accent)] [--button-hover:var(--color-accent-hover)] [--button-content:var(--color-accent-content)] [--button-foreground:var(--color-accent-foreground)]',
        'neutral' => '[--button-color:var(--color-foreground)] [--button-hover:var(--color-muted)] [--button-content:var(--color-foreground)] [--button-foreground:var(--color-surface)]',
    ];

    $variants = [
        'solid' => 'border-transparent bg-(--button-color) text-(--button-foreground) enabled:hover:bg-(--button-hover) enabled:active:bg-(--button-hover)',
        'outline' => 'border-current bg-transparent text-(--button-content) enabled:hover:bg-(--button-color)/10 enabled:active:bg-(--button-color)/15',
        'ghost' => 'border-transparent bg-transparent text-(--button-content) enabled:hover:bg-(--button-color)/10 enabled:active:bg-(--button-color)/15',
    ];

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
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-(--button-content)',
        '[&_svg]:size-4 [&_svg]:shrink-0',
        $colors[$color] ?? $colors['primary'],
        $variants[$variant] ?? $variants['solid'],
        $sizes[$size] ?? $sizes['md'],
        $radii[$radius] ?? $radii['default'],
        'w-full' => $fullWidth,
    ]) }}
>
    {{ $slot }}
</button>
