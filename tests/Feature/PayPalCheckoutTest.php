<?php

use App\Models\SafetyDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('success route marks document as paid when paypal payment is completed', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'stripe_session_id' => 'PAYPAL_ORDER_123',
        'is_paid' => false,
    ]);

    Mockery::mock('overload:Srmklive\PayPal\Services\PayPal')
        ->shouldReceive('setApiCredentials')
        ->shouldReceive('getAccessToken')->andReturn('fake_token')
        ->shouldReceive('capturePaymentOrder')->andReturn(['status' => 'COMPLETED']);

    $this->actingAs($user)
        ->get(route('paypal.success', ['document' => $doc->id, 'token' => 'PAYPAL_ORDER_123']))
        ->assertRedirect();

    expect($doc->fresh()->is_paid)->toBeTrue();
});

test('success route does not mark document as paid when paypal payment fails', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'stripe_session_id' => 'PAYPAL_ORDER_456',
        'is_paid' => false,
    ]);

    Mockery::mock('overload:Srmklive\PayPal\Services\PayPal')
        ->shouldReceive('setApiCredentials')
        ->shouldReceive('getAccessToken')->andReturn('fake_token')
        ->shouldReceive('capturePaymentOrder')->andReturn(['status' => 'FAILED']);

    $this->actingAs($user)
        ->get(route('paypal.success', ['document' => $doc->id, 'token' => 'PAYPAL_ORDER_456']))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($doc->fresh()->is_paid)->toBeFalse();
});
