<?php

use App\Models\User;
use App\Models\SafetyDocument;
use Illuminate\Support\Facades\Http;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('user can access dashboard and see their documents', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'project_name' => 'Test Project',
        'company_name' => 'Test Company',
        'project_location' => 'Site X',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'download_ready' => true,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertStatus(200)
        ->assertSee('Test Project')
        ->assertSee('Test Company');
});

test('user can access generate page', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get('/generate/jha')
        ->assertStatus(200)
        ->assertSee('Generate Job Hazard Analysis');
});

test('user can submit generation form', function () {
    $user = User::factory()->create();

    // Mock AI Response
    Http::fake([
        '*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'steps' => [
                                [
                                    'step' => 1,
                                    'step_description' => 'Test Step',
                                    'hazards' => ['Hazard 1'],
                                    'controls' => ['Control 1'],
                                    'rac' => 'L'
                                ]
                            ]
                        ])
                    ]
                ]
            ]
        ], 200)
    ]);

    $component = Volt::test('pages.generate', ['type' => 'jha'])
        ->set('projectName', 'New Project')
        ->set('company', 'My Company')
        ->set('location', 'Site A')
        ->set('preparedBy', 'Officer A')
        ->set('projectDescription', 'Description of project')
        ->set('equipmentTools', 'Equipment List')
        ->call('generate');

    $this->assertDatabaseHas('safety_documents', [
        'project_name' => 'New Project',
        'document_type' => 'JHA'
    ]);

    $doc = SafetyDocument::where('project_name', 'New Project')->first();
    $component->assertRedirect(route('preview', ['id' => $doc->id]));
});

test('user can view document preview', function () {
    $user = User::factory()->create();
    $doc = SafetyDocument::create([
        'user_id' => $user->id,
        'project_name' => 'Preview Project',
        'company_name' => 'Preview Co',
        'project_location' => 'Site B',
        'project_description' => 'Desc',
        'equipment_tools' => 'Tools',
        'document_type' => 'JHA',
        'ai_response' => ['steps' => [['step' => '1', 'step_description' => 'Task', 'hazards' => ['H'], 'controls' => ['C'], 'rac' => 'L']]],
    ]);

    $this->actingAs($user)
        ->get(route('preview', ['id' => $doc->id]))
        ->assertStatus(200)
        ->assertSee('Preview Project')
        ->assertSee('Task');
});
