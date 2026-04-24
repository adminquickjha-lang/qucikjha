<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

test('socialite redirects to provider', function () {
    $provider = 'google';

    // For redirect, we just want to see it calling the driver and redirecting
    // But since it calls through Socialite facade, let's mock it
    $mock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $mock->shouldReceive('redirect')->andReturn(redirect('https://google.com'));

    Socialite::shouldReceive('driver')->with($provider)->andReturn($mock);

    $response = $this->get(route('social.redirect', ['provider' => $provider]));

    $response->assertRedirect('https://google.com');
});

test('socialite login creates new user if doesn\'t exist', function () {
    $provider = 'google';

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('123456');
    $abstractUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('New User');

    $providerMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $providerMock->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($providerMock);

    $response = $this->get(route('social.callback', ['provider' => $provider]));

    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'name' => 'New User',
        'google_id' => '123456',
    ]);

    $user = User::where('email', 'newuser@example.com')->first();
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('user-dashboard', absolute: false));
});

test('socialite login logs in existing user', function () {
    $provider = 'google';
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'name' => 'Existing User',
        'google_id' => '654321',
    ]);

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('654321');
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Existing User Updated');

    $providerMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $providerMock->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($providerMock);

    $response = $this->get(route('social.callback', ['provider' => $provider]));

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('user-dashboard', absolute: false));
});

test('socialite login handles failure', function () {
    $provider = 'google';

    Socialite::shouldReceive('driver')->with($provider)->andThrow(new Exception('Provider failed'));

    $response = $this->get(route('social.callback', ['provider' => $provider]));

    $response->assertRedirect('/login');
    $this->assertGuest();
});
