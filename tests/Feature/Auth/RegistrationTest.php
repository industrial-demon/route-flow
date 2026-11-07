<?php

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Features;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSeeLivewire('features.auth.register')
        ->assertSee('Create an account')
        ->assertSee('wire:submit="save"', false);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    expect(User::where('email', 'test@example.com')->sole()->roles->pluck('slug')->all())
        ->toBe([UserRoles::BAYER->value]);
});

test('registration assigns the selected account role', function (string $role) {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'userVariant' => $role,
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->sole();

    expect($user->roles->pluck('slug')->all())->toBe([$role]);
    $this->assertAuthenticatedAs($user);
})->with([
    'buyer' => UserRoles::BAYER->value,
    'seller' => UserRoles::SELLER->value,
]);

test('livewire registration uses the selected account type', function (string $role) {
    Event::fake([Registered::class]);

    Livewire::test('features.auth.register')
        ->assertSet('userVariant', UserRoles::BAYER->value)
        ->assertSee('Buyer')
        ->assertSee('Seller')
        ->set('name', 'John Doe')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('userVariant', $role)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->sole();

    expect($user->roles->pluck('slug')->all())
        ->toBe([$role]);
    $this->assertAuthenticatedAs($user);
    Event::assertDispatched(Registered::class, fn (Registered $event) => $event->user->is($user));
})->with([
    'buyer' => UserRoles::BAYER->value,
    'seller' => UserRoles::SELLER->value,
]);

test('invalid livewire registration shows validation errors without creating a user', function () {
    Livewire::test('features.auth.register')
        ->set('name', 'John Doe')
        ->set('email', 'invalid-email')
        ->set('password', 'password')
        ->set('password_confirmation', 'different-password')
        ->set('userVariant', UserRoles::SUPER_ADMIN->value)
        ->call('save')
        ->assertHasErrors(['email', 'password', 'userVariant']);

    $this->assertDatabaseCount('users', 0);
    $this->assertGuest();
});

test('registration rejects unsupported account roles', function (string $role) {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'userVariant' => $role,
    ]);

    $response->assertSessionHasErrors('userVariant');

    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    $this->assertDatabaseCount('user_roles', 0);
    $this->assertGuest();
})->with([
    'administrator' => UserRoles::SUPER_ADMIN->value,
    'logist' => UserRoles::OUTSOURCE_LOGIST->value,
    'unknown' => 'INVALID_ROLE',
    'empty' => '',
]);
