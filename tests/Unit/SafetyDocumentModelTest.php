<?php

use App\Models\SafetyDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ================================================
// SafetyDocument Model Tests
// ================================================

test('safety document can be created with all fields', function () {
    $user = User::factory()->create();

    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Test Company',
        'project_name' => 'Test Project',
        'project_location' => 'Site A',
        'project_description' => 'Testing description',
        'equipment_tools' => 'Hammer, Drill',
        'document_type' => 'JHA',
        'regulations' => ['OSHA 1926', 'EM 385-1-1'],
        'ai_response' => ['steps' => [['step' => 'Step 1', 'hazards' => ['Hazard'], 'controls' => ['Control'], 'rac' => 'L']]],
        'is_paid' => false,
        'download_ready' => true,
        'amount' => 19.90,
    ]);

    expect($doc->project_name)->toBe('Test Project');
    expect($doc->document_type)->toBe('JHA');
    expect($doc->regulations)->toBeArray();
    expect($doc->ai_response)->toBeArray();
    expect($doc->is_paid)->toBeFalse();
    expect($doc->download_ready)->toBeTrue();
});

test('safety document belongs to a user', function () {
    $user = User::factory()->create();

    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
    ]);

    expect($doc->user)->toBeInstanceOf(User::class);
    expect($doc->user->id)->toBe($user->id);
});

test('user has many safety documents', function () {
    $user = User::factory()->create();

    SafetyDocument::create([
        'user_id' => $user->id, 'company_name' => 'Co', 'project_name' => 'P1',
        'project_location' => 'L', 'project_description' => 'D', 'equipment_tools' => 'T', 'document_type' => 'JHA',
    ]);
    SafetyDocument::create([
        'user_id' => $user->id, 'company_name' => 'Co', 'project_name' => 'P2',
        'project_location' => 'L', 'project_description' => 'D', 'equipment_tools' => 'T', 'document_type' => 'AHA',
    ]);

    expect($user->safetyDocuments)->toHaveCount(2);
});

test('ai_response is cast to array', function () {
    $user = User::factory()->create();
    $response = ['steps' => [['step' => 'Inspection', 'hazards' => ['Fall'], 'controls' => ['Harness'], 'rac' => 'H']]];

    $doc = SafetyDocument::create([
        'user_id' => $user->id, 'company_name' => 'Co', 'project_name' => 'P',
        'project_location' => 'L', 'project_description' => 'D', 'equipment_tools' => 'T',
        'document_type' => 'JHA', 'ai_response' => $response,
    ]);

    $fresh = SafetyDocument::find($doc->id);
    expect($fresh->ai_response)->toBeArray();
    expect($fresh->ai_response['steps'])->toHaveCount(1);
    expect($fresh->ai_response['steps'][0]['rac'])->toBe('H');
});

test('regulations is cast to array', function () {
    $user = User::factory()->create();

    $doc = SafetyDocument::create([
        'user_id' => $user->id, 'company_name' => 'Co', 'project_name' => 'P',
        'project_location' => 'L', 'project_description' => 'D', 'equipment_tools' => 'T',
        'document_type' => 'JHA', 'regulations' => ['OSHA 1926', 'OSHA 1910'],
    ]);

    $fresh = SafetyDocument::find($doc->id);
    expect($fresh->regulations)->toBeArray();
    expect($fresh->regulations)->toContain('OSHA 1926');
});

test('is_paid defaults to false', function () {
    $user = User::factory()->create();

    $doc = SafetyDocument::create([
        'user_id' => $user->id, 'company_name' => 'Co', 'project_name' => 'P',
        'project_location' => 'L', 'project_description' => 'D', 'equipment_tools' => 'T',
        'document_type' => 'JHA',
    ]);

    expect($doc->is_paid)->toBeFalsy();
});

test('safety document can be marked as paid', function () {
    $user = User::factory()->create();

    $doc = SafetyDocument::create([
        'user_id' => $user->id, 'company_name' => 'Co', 'project_name' => 'P',
        'project_location' => 'L', 'project_description' => 'D', 'equipment_tools' => 'T',
        'document_type' => 'JHA', 'is_paid' => false,
    ]);

    $doc->update(['is_paid' => true]);

    expect($doc->fresh()->is_paid)->toBeTrue();
});
