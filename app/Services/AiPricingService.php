<?php

namespace App\Services;

class AiPricingService
{
    /**
     * Calculate cost for Claude 3.5 Sonnet.
     * Prices per 1M tokens: $3.00 input, $15.00 output.
     */
    public static function calculateCost(int $inputTokens, int $outputTokens, string $model = 'claude-3-5-sonnet'): float
    {
        $prices = [
            'claude-3-5-sonnet' => [
                'input' => 0.000003,
                'output' => 0.000015,
            ],
            'claude-3-opus' => [
                'input' => 0.000015,
                'output' => 0.000075,
            ],
            'claude-3-haiku' => [
                'input' => 0.00000025,
                'output' => 0.00000125,
            ],
            'gpt-4o' => [
                'input' => 0.000005,
                'output' => 0.000015,
            ],
            'openrouter/free' => [
                'input' => 0.0,
                'output' => 0.0,
            ],
        ];

        $modelPrice = $prices[$model] ?? $prices['claude-3-5-sonnet'];

        return ($inputTokens * $modelPrice['input']) + ($outputTokens * $modelPrice['output']);
    }
}
