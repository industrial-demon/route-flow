@props([
    'name',
    'options',
    'label' => null,
    'value' => null,
    'variant' => 'cards',
    'color' => 'primary',
    'orientation' => 'horizontal',
    'description' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'id' => null,
])

@php
    $id ??= 'radio-group-'.substr(sha1($name), 0, 12);
    $error ??= $errors->first($name);
    $describedBy = trim(($description ? $id.'-description' : '').' '.($error ? $id.'-error' : ''));
    $modelAttributes = $attributes->whereStartsWith(['wire:model', 'x-model']);

    $colors = [
        'primary' => '[--radio-color:var(--color-primary)] [--radio-content:var(--color-primary-content)]',
        'accent' => '[--radio-color:var(--color-accent)] [--radio-content:var(--color-accent-content)]',
        'neutral' => '[--radio-color:var(--color-foreground)] [--radio-content:var(--color-foreground)]',
    ];
@endphp

<fieldset
    id="{{ $id }}"
    @disabled($disabled)
    @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
    @if ($error) aria-invalid="true" @endif
    {{ $attributes->whereDoesntStartWith(['wire:model', 'x-model'])->class([
        'min-w-0 space-y-2',
        $colors[$color] ?? $colors['primary'],
    ]) }}
>
    @if ($label)
        <legend class="text-sm font-medium text-foreground">{{ $label }}</legend>
    @endif

    @if ($description)
        <p id="{{ $id }}-description" class="text-sm text-muted">{{ $description }}</p>
    @endif

    <div @class([
        'flex gap-3',
        'flex-col' => $orientation === 'vertical',
        'flex-wrap' => $orientation !== 'vertical',
    ])>
        @foreach ($options as $optionValue => $option)
            @php
                $option = is_array($option) ? $option : ['label' => $option];
                $optionDisabled = $disabled || ($option['disabled'] ?? false);
            @endphp

            <label
                for="{{ $id }}-{{ $loop->index }}"
                @class([
                    'flex min-w-0 items-start gap-3 text-sm text-foreground',
                    'cursor-pointer has-disabled:cursor-not-allowed has-disabled:opacity-50',
                    'transition-colors motion-reduce:transition-none',
                    'flex-1 rounded-input border border-border bg-surface p-4 has-checked:border-(--radio-color) has-checked:bg-(--radio-color)/5 has-checked:text-(--radio-content) has-[:enabled]:hover:border-(--radio-color)' => $variant === 'cards',
                    'py-1' => $variant !== 'cards',
                ])
            >
                <input
                    id="{{ $id }}-{{ $loop->index }}"
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $optionValue }}"
                    class="mt-0.5 size-4 shrink-0 cursor-inherit accent-(--radio-color) focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-(--radio-content)"
                    @checked($value !== null && (string) $value === (string) $optionValue)
                    @required($required)
                    @disabled($optionDisabled)
                    @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                    @if ($error) aria-invalid="true" @endif
                    {{ $modelAttributes }}
                />

                <span class="min-w-0">
                    <span class="block font-medium">{{ $option['label'] }}</span>
                    @if (! empty($option['description']))
                        <span class="mt-1 block text-sm text-muted">{{ $option['description'] }}</span>
                    @endif
                </span>
            </label>
        @endforeach
    </div>

    @if ($error)
        <p id="{{ $id }}-error" class="text-sm text-accent-content" role="alert">{{ $error }}</p>
    @endif
</fieldset>
