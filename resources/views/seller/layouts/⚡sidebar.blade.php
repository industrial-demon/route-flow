<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

@php
    $menuItems = [
        'products' => ['label' => 'Products', 'icon' => 'projects'],
        'profile' => ['label' => 'Profile', 'icon' => 'employees'],
        'connections' => ['label' => 'Connections', 'icon' => 'messenger'],
        'management' => ['label' => 'Management', 'icon' => 'settings_input'],
        'statistics' => ['label' => 'Statistics', 'icon' => 'dashboard'],
    ];
@endphp

<nav aria-label="{{ __('Seller navigation') }}"
    class="h-full w-full overflow-y-auto border-r border-border bg-surface">
    <div class="flex flex-col gap-2 px-3 py-5">

        @php
                $isActive = request()->routeIs('seller.');
        @endphp


        @foreach ($menuItems as $key => $item)
            @php
                $isActive = request()->routeIs('seller.' . $key);
            @endphp
            <a href="{{ route('seller.' . $key) }}" wire:navigate
                data-active="{{ $isActive ? 'true' : 'false' }}"
                @if ($isActive) aria-current="page" @endif
                class="group w-full shrink-0 cursor-pointer border-l-[3px] border-transparent p-1 text-left text-sm font-medium text-foreground transition-colors focus-visible:outline-none data-[active=true]:border-primary data-[active=true]:text-primary sm:text-base">
                <span
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 transition-colors group-hover:bg-primary/10 group-hover:text-primary group-focus-visible:bg-primary/10 group-focus-visible:text-primary group-focus-visible:ring-2 group-focus-visible:ring-primary">
                    <span aria-hidden="true" class="flex shrink-0 items-center">
                        <x-dynamic-component :component="'icons.' . $item['icon']" width="20" height="20" />
                    </span>
                    {{ __($item['label']) }}
                </span>
            </a>
        @endforeach
    </div>
</nav>
