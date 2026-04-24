<?php

use App\Models\User;
use App\Models\SafetyDocument;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Barryvdh\DomPDF\Facade\Pdf;

uses(RefreshDatabase::class);

test('user cannot download pdf if unpaid and not the owner', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    
    $doc = SafetyDocument::create([
        'user_id' => $owner->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => false,
    ]);

    $this->actingAs($user)
        ->get(route('document.pdf', ['id' => $doc->id]))
        ->assertStatus(403);
});

test('user can download pdf if paid', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    
    $doc = SafetyDocument::create([
        'user_id' => $owner->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => true,
    ]);

    $this->actingAs($user)
        ->get(route('document.pdf', ['id' => $doc->id]))
        ->assertStatus(200);
});

test('owner can download pdf even if unpaid', function () {
    $owner = User::factory()->create();
    
    $doc = SafetyDocument::create([
        'user_id' => $owner->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => false,
    ]);

    $this->actingAs($owner)
        ->get(route('document.pdf', ['id' => $doc->id]))
        ->assertStatus(200);
});

test('user cannot download word if unpaid and not the owner', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    
    $doc = SafetyDocument::create([
        'user_id' => $owner->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => false,
    ]);

    $this->actingAs($user)
        ->get(route('document.word', ['id' => $doc->id]))
        ->assertStatus(403);
});

test('user can download word if paid', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    
    $doc = SafetyDocument::create([
        'user_id' => $owner->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => true,
    ]);

    $this->actingAs($user)
        ->get(route('document.word', ['id' => $doc->id]))
        ->assertStatus(200);
});

test('owner can download word even if unpaid', function () {
    $owner = User::factory()->create();
    
    $doc = SafetyDocument::create([
        'user_id' => $owner->id,
        'company_name' => 'Co',
        'project_name' => 'Proj',
        'project_location' => 'Loc',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'is_paid' => false,
    ]);

    $this->actingAs($owner)
        ->get(route('document.word', ['id' => $doc->id]))
        ->assertStatus(200);
});
