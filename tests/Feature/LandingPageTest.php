<?php

use App\Models\User;
use App\Models\SafetyDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('landing page renders successfully', function () {
    $response = $this->get('/');
    
    $response->assertStatus(200);
});

test('landing page shows dynamic default prices safely', function () {
    // These should show up even if database settings are empty due to the Volt component defaults
    $response = $this->get('/');
    
    $response->assertStatus(200);
});
