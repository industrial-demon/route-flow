# App theme and components

Theme tokens live in `resources/css/app.css`. Green is `primary`; the red
`accent` is exactly `hsla(0, 100%, 35%, 1)`. Both remain available in dark mode.
Use `bg-background`, `bg-surface`, `text-foreground`, `text-muted`,
`border-border`, and `bg-subtle` for app surfaces and content. Shared radius
utilities are `rounded-button` (10px), `rounded-input` (10px), and
`rounded-card` (14px).

The screenshot examples can be built with the reusable Blade button:

```blade
{{-- 160 × 40px, green, 10px radius --}}
<x-button class="w-40">New Connection</x-button>

{{-- 78 × 35px, red accent, 10px radius --}}
<x-button color="accent" size="sm" class="w-[78px]">Stop</x-button>

{{-- 91 × 35px, neutral outline, 10px radius --}}
<x-button color="neutral" size="sm" outline class="w-[91px]">Cancel</x-button>

<x-button color="accent" size="sm">Delete</x-button>
```

| Prop | Default | Values |
| --- | --- | --- |
| `type` | `button` | Native button types (`button`, `submit`, `reset`) |
| `variant` | `solid` | `solid`, `outline`, `ghost` |
| `color` | `primary` | `primary` (green), `accent` (red), `neutral` |
| `size` | `md` | `sm` (35px), `md` (40px), `lg` (48px) |
| `radius` | `default` | `default` (10px), `none`, `sm`, `md`, `lg`, `full` |
| `outline` | `false` | Boolean shortcut for `variant="outline"` |
| `disabled` | `false` | Native disabled state |
| `full-width` | `false` | Fill the available width |

Pass booleans with Blade bindings for dynamic values: `:disabled="$saving"`,
`:outline="$outlined"`, or `:full-width="true"`. Bare `outline` and `disabled`
attributes also enable their respective states.

The component forwards HTML, Alpine, and Livewire attributes to the native
button. It defaults to `type="button"` so it does not submit a surrounding form
accidentally. Use `type="submit"` explicitly for form actions.

```blade
<x-button type="submit" :full-width="true">Save changes</x-button>

<x-button variant="outline" radius="full" wire:click="cancel">
    Cancel
</x-button>

<x-button wire:click="connect" wire:loading.attr="disabled" wire:target="connect">
    <flux:icon.plus aria-hidden="true" />
    New Connection
</x-button>
```

Buttons include hover, active, keyboard focus, reduced motion, and disabled
styles. Put icons or other content in the default slot; supply an `aria-label`
when using an icon without visible text.

The screenshots are button references, not complete page designs. Surface
colors are defaults based on those references; the Figma design tokens have
not been inspected directly.

## Radio group

`<x-radio-group>` accepts an associative array of values and labels. It uses
native radio inputs, so selecting an option deselects the others, arrow keys
change the selection, and standard form submission includes the selected value.

```blade
<x-radio-group
    name="userVariant"
    label="Account type"
    :options="['BAYER' => 'Buyer', 'SELLER' => 'Seller']"
    :value="$userVariant"
    wire:model="userVariant"
    required
/>
```

This example is included in both registration forms. `BAYER` is the
existing buyer enum value in this app; the visible label is "Buyer".

Registration passes `userVariant` to `CreateNewUser`, which accepts only
`BAYER` and `SELLER` and assigns the corresponding existing role through
`user_roles`. It creates the allowed role if it has not been seeded. The user
and role assignment run in one transaction. `userVariant` is only a form value;
there is no extra user column. Registrations without the value default to Buyer.

For a standard Blade form, pass `:value="old('userVariant', 'BAYER')"` and omit
`wire:model`. Alpine's `x-model` and Livewire's `wire:model` modifiers are
forwarded to every input; other attributes, including `class`, go on the group.

| Prop | Default | Values |
| --- | --- | --- |
| `name` | Required | Form field name |
| `options` | Required | Value-to-label map, or value-to-option map shown below |
| `label` | `null` | Visible group legend; supply this or an `aria-label` |
| `value` | `null` | Initially selected value |
| `variant` | `cards` | `cards` (10px radius) or `default` (plain radios) |
| `color` | `primary` | `primary`, `accent`, `neutral` |
| `orientation` | `horizontal` | `horizontal`, `vertical` |
| `description` | `null` | Help text for the group |
| `error` | Validation error for `name` | Explicit error text override |
| `required` | `false` | Require a selection |
| `disabled` | `false` | Disable every option |
| `id` | Derived from `name` | Stable across Livewire updates; override when repeating the same name in separate forms |

Use option arrays to add descriptions or disable an individual option:

```blade
<x-radio-group
    name="account_type"
    label="Create your account as"
    variant="default"
    orientation="vertical"
    color="accent"
    :options="[
        'BAYER' => ['label' => 'Buyer', 'description' => 'Buy products.'],
        'SELLER' => ['label' => 'Seller', 'description' => 'Sell your products.'],
    ]"
    :value="old('account_type')"
/>
```

Option arrays may also set `'disabled' => true`. For boolean props use bare
attributes such as `required` or bound values such as `:disabled="$saving"`.
Group help and errors are linked to the inputs for assistive technology.

## Ghost input

`<x-ghost-input>` is a transparent input with an underline and floating label.
The label appears in the empty input's text position, moves above the text on
focus, and stays above when the input contains a value. It never becomes the
input value. This behavior uses CSS and also works with prefilled values and
Livewire updates.

```blade
<x-ghost-input
    name="email"
    label="Email address"
    type="email"
    :value="old('email')"
    autocomplete="email"
    required
/>

<x-ghost-input
    name="password"
    label="Password"
    type="password"
    wire:model="password"
    autocomplete="new-password"
    viewable
    required
/>
```

| Prop | Default | Values |
| --- | --- | --- |
| `name` | Required | Form field name |
| `label` | Required | Floating label, separate from the input value |
| `type` | `text` | Native input type, such as `text`, `email`, `password`, or `tel` |
| `value` | `null` | Initial value for a standard Blade form |
| `color` | `primary` | `primary`, `accent`, `neutral` |
| `viewable` | `false` | Show an accessible visibility toggle for password inputs |
| `required` | `false` | Require a value |
| `disabled` | `false` | Disable the input and its visibility toggle |
| `description` | `null` | Help text below the underline |
| `error` | Validation error for `name` | Explicit error text override |
| `id` | Derived from `name` | Stable across Livewire updates; override for repeated names |

HTML, Alpine, and Livewire attributes, including `class`, are forwarded to the
input. Pass `wire:model` or `x-model` as usual. The component reserves the
placeholder as a single space for CSS empty-state detection; use `label` for
the visible prompt. Password visibility uses the Alpine already bundled with
Livewire. The floating label itself does not need JavaScript.

Both registration forms use this component. Error messages automatically use
the red accent and are linked to the input along with any description.
