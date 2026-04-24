<?php

use Illuminate\Support\Facades\Artisan;
use Laravel\Ai\AnonymousAgent;

Artisan::command('test:ai', function () {
    $this->info('Testing AI connection...');
    
    try {
        $agent = new AnonymousAgent(
            instructions: 'You are a test assistant.',
            messages: [],
            tools: []
        );

        $this->comment('Prompting: claude-sonnet-4-6 via default provider (anthropic)');
        
        $response = $agent->prompt('Say hello', model: 'claude-sonnet-4-6');
        
        $this->info('Response: ' . $response->text);
    } catch (\Exception $e) {
        $this->error('Error: ' . $e->getMessage());
    }
})->purpose('Test the AI connection');
