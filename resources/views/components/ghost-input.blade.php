@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'color' => 'primary',
    'viewable' => false,
    'required' => false,
    'disabled' => false,
    'description' => null,
    'error' => null,
    'id' => null,
])

@php
    $id ??= 'ghost-input-'.substr(sha1($name), 0, 12);
    $error ??= $errors->first($name);
    $showToggle = $viewable && $type === 'password';
    $describedBy = trim(($attributes->get('aria-describedby') ?? '').' '.($description ? $id.'-description' : '').' '.($error ? $id.'-error' : ''));

    $colors = [
        'primary' => '[--input-color:var(--color-primary)] [--input-content:var(--color-primary-content)]',
        'accent' => '[--input-color:var(--color-accent)] [--input-content:var(--color-accent-content)]',
        'neutral' => '[--input-color:var(--color-foreground)] [--input-content:var(--color-foreground)]',
    ];
@endphp

<div
    @class(['w-full', $error ? $colors['accent'] : ($colors[$color] ?? $colors['primary'])])
    @if ($showToggle) x-data="{ revealed: false }" @endif
>
    <div class="relative">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($value !== null) value="{{ $value }}" @endif
            @required($required)
            @disabled($disabled)
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($error) aria-invalid="true" @endif
            @if ($showToggle)
                x-ref="input"
                x-bind:type="revealed ? 'text' : 'password'"
            @endif
            {{ $attributes->except(['aria-describedby', 'placeholder', ...($error ? ['aria-invalid'] : [])])->class([
                'peer block h-16 w-full appearance-none rounded-none border-0 border-b bg-transparent px-0 pt-6 pb-2 text-base text-foreground shadow-none outline-none',
                'transition-colors placeholder:text-transparent focus:border-(--input-color) focus:ring-0 motion-reduce:transition-none',
                'disabled:cursor-not-allowed disabled:opacity-50',
                'border-border' => ! $error,
                'border-accent' => (bool) $error,
                'pe-12' => $showToggle,
            ])->merge(['placeholder' => ' ']) }}
        />

        <label
            for="{{ $id }}"
            @class([
                'pointer-events-none absolute start-0 top-2 max-w-full truncate text-xs text-muted',
                'transition-[top,font-size,color] duration-150 motion-reduce:transition-none',
                'peer-[:placeholder-shown:not(:focus)]:top-6 peer-[:placeholder-shown:not(:focus)]:text-base',
                'peer-focus:text-(--input-content) peer-disabled:opacity-50',
                'pe-12' => $showToggle,
            ])
        >{{ $label }}</label>

        @if ($showToggle)
            <button
                type="button"
                class="absolute end-0 bottom-1 flex size-10 items-center justify-center rounded-button text-muted hover:text-foreground focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-(--input-content) disabled:cursor-not-allowed disabled:opacity-50"
                aria-controls="{{ $id }}"
                aria-label="{{ __('Show password') }}"
                aria-pressed="false"
                x-bind:aria-label="revealed ? @js(__('Hide password')) : @js(__('Show password'))"
                x-bind:aria-pressed="revealed"
                x-on:click="revealed = !revealed; $nextTick(() => $refs.input.focus({ preventScroll: true }))"
                @disabled($disabled)
            >
                <flux:icon.eye class="size-5" x-show="!revealed" aria-hidden="true" />
                <flux:icon.eye-slash class="size-5" x-show="revealed" x-cloak aria-hidden="true" />
            </button>
        @endif
    </div>

    @if ($description)
        <p id="{{ $id }}-description" class="mt-2 text-sm text-muted">{{ $description }}</p>
    @endif

    @if ($error)
        <p id="{{ $id }}-error" class="mt-2 text-sm text-accent-content" role="alert">{{ $error }}</p>
    @endif
</div>
