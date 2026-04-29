<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('non-admin user cannot access admin dashboard', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertStatus(403);
});

test('guest cannot access admin dashboard', function () {
    $this->get(route('admin.dashboard'))->assertRedirect('/login');
});

test('admin user can access admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('Admin Dashboard');
});

test('admin can update dynamic settings via livewire component', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin);

    Volt::test('pages.admin.pricing')
        ->set('jhaPrice', '39.99')
        ->set('ahaPrice', '29.99')
        ->call('handleSave');

    $this->assertDatabaseHas('settings', [
        'key' => 'jha_price',
        'value' => '39.99',
    ]);
});
