<?php

use Livewire\Component;
use App\Actions\Fortify\CreateNewUser;
use App\Enums\UserRoles;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $userVariant = UserRoles::BAYER->value;

    public function save(CreateNewUser $createNewUser): void
    {
        $createNewUser->create(['name' => $this->name, 'email' => $this->email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'userVariant' => $this->userVariant]);
    }

    public function render()
    {
        return $this->view()->layout('layouts::auth.simple');
    }
};
?>

<div>
    HEELLOP

    <form wire:submit='save' x-data="{ a: 1 }">
        Username: <strong x-text="a"></strong>
        <x-ghost-input name="name" :label="__('Name')" wire:model="name" autocomplete="name" required />
        <x-ghost-input name="email" :label="__('Email address')" type="email" wire:model="email" autocomplete="email"
            required />
        <x-ghost-input name="password" :label="__('Password')" type="password" wire:model="password"
            autocomplete="new-password" viewable required />
        <x-ghost-input name="password_confirmation" :label="__('Confirm password')" type="password"
            wire:model="password_confirmation" autocomplete="new-password" viewable required />
        <x-radio-group name="userVariant" :label="__('Account type')" :options="[
            \App\Enums\UserRoles::BAYER->value => __('Buyer'),
            \App\Enums\UserRoles::SELLER->value => __('Seller'),
        ]" :value="$userVariant" wire:model="userVariant" required />
        <button type="submit">Save</button>

        <x-button type="button" x-on:click="a++">Save</x-button>

    </form>
</div>