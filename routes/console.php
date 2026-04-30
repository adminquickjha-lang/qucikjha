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

        $model = 'openrouter/free';
        $this->comment("Prompting: {$model} via default provider");

        $response = $agent->prompt('Say hello', model: $model);

        $this->info('Response: '.$response->text);
    } catch (Exception $e) {
        $this->error('Error: '.$e->getMessage());
    }
})->purpose('Test the AI connection');
