<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('brief page renders successfully', function () {
    $response = $this->get('/brief');

    $response->assertStatus(200)
        ->assertSee('Full Project Specification')
        ->assertSee('QuickJHA Technical Requirements');
});

test('guest can access brief page', function () {
    $this->get('/brief')->assertStatus(200);
});

test('authenticated user can access brief page', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/brief')->assertStatus(200);
});
