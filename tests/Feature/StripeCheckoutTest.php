<?php

use App\Models\User;
use App\Models\SafetyDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('success route updates document as paid when session id matches', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'stripe_session_id' => 'cs_test_12345',
        'is_paid' => false,
    ]);

    $this->actingAs($user)
        ->get(route('stripe.success', ['document' => $doc->id, 'session_id' => 'cs_test_12345']))
        ->assertRedirect(route('preview', ['id' => $doc->id]))
        ->assertSessionHas('success');

    expect($doc->fresh()->is_paid)->toBeTrue();
});

test('success route does not update document if session id is invalid', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'stripe_session_id' => 'cs_test_12345',
        'is_paid' => false,
    ]);

    $this->actingAs($user)
        ->get(route('stripe.success', ['document' => $doc->id, 'session_id' => 'invalid_session']))
        ->assertRedirect(route('preview', ['id' => $doc->id]))
        ->assertSessionHas('error');

    expect($doc->fresh()->is_paid)->toBeFalse();
});

test('checkout route redirects to stripe', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => false,
    ]);

    // Mock Stripe\Checkout\Session::create
    Mockery::mock('alias:Stripe\Checkout\Session')
        ->shouldReceive('create')
        ->once()
        ->andReturn((object)[
            'id' => 'cs_test_new',
            'url' => 'https://checkout.stripe.com/pay/test'
        ]);

    $this->actingAs($user)
        ->get(route('stripe.checkout', ['document' => $doc->id]))
        ->assertRedirect('https://checkout.stripe.com/pay/test');

    expect($doc->fresh()->stripe_session_id)->toBe('cs_test_new');
    expect($doc->fresh()->amount)->toEqual(19.9);

});

test('stripe webhook updates document status', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => false,
    ]);

    // Construct a mock event object that looks like Stripe's
    $event = (object)[
        'type' => 'checkout.session.completed',
        'data' => (object)[
            'object' => (object)[
                'metadata' => (object)[
                    'document_id' => $doc->id
                ]
            ]
        ]
    ];

    // Mock Stripe\Webhook::constructEvent
    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->once()
        ->andReturn($event);

    $response = $this->postJson(route('stripe.webhook'), [], [
        'Stripe-Signature' => 'fake_signature'
    ]);

    $response->assertStatus(200);
    expect($doc->fresh()->is_paid)->toBeTrue();
});
