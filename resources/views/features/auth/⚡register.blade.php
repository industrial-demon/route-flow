<?php

use App\Actions\Fortify\CreateNewUser;
use App\Enums\UserRoles;
use App\Enums\UserType;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $userVariant = UserType::BUYER->value;

    public function mount(): void
    {
        abort_unless(Features::enabled(Features::registration()), 404);

        foreach (['name', 'email', 'userVariant'] as $field) {
            $value = old($field);

            if (is_string($value)) {
                $this->{$field} = $value;
            }
        }
    }

    public function save(CreateNewUser $createNewUser, StatefulGuard $guard): void
    {
        abort_unless(Features::enabled(Features::registration()), 404);
        abort_if($guard->check(), 403);

        if (config('fortify.lowercase_usernames')) {
            $this->email = Str::lower($this->email);
        }

        $user = $createNewUser->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'userVariant' => $this->userVariant,
        ]);

        event(new Registered($user));

        $guard->login($user);
        session()->regenerate();

        $this->redirectIntended(default: Fortify::redirects('register'), navigate: true);
    }

    public function render()
    {
        return $this->view()->layout('layouts::auth', ['title' => __('Register')]);
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form  wire:submit="save" class="flex flex-col gap-6" novalidate>
        <x-radio-group
            name="userVariant"
            :label="__('Account type')"
            :options="[
               \App\Enums\UserType::SELLER->value => __('Seller'), 
                \App\Enums\UserType::BUYER->value => __('Buyer'),
                \App\Enums\UserType::LOGISTICS->value => __('LOGISTICS'),
            ]"
            :value="$userVariant"
            wire:model="userVariant"
            required
        />

        <x-ghost-input
            name="name"
            :label="__('Name')"
            wire:model="name"
            type="text"
            required
            autofocus
            autocomplete="name"
        />

        <x-ghost-input
            name="email"
            :label="__('Email address')"
            wire:model="email"
            type="email"
            required
            autocomplete="email"
        />

        <x-ghost-input
            name="password"
            :label="__('Password')"
            wire:model="password"
            type="password"
            required
            autocomplete="new-password"
            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            viewable
        />

        <x-ghost-input
            name="password_confirmation"
            :label="__('Confirm password')"
            wire:model="password_confirmation"
            type="password"
            required
            autocomplete="new-password"
            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            viewable
        />

        <div class="flex items-center justify-end">
            <x-button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                {{ __('Create account') }}
            </x-button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
