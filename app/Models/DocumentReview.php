<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentReview extends Model
{
    protected $fillable = [
        'safety_document_id',
        'prompt',
        'input_tokens',
        'output_tokens',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:6',
    ];

    public function safetyDocument()
    {
        return $this->belongsTo(SafetyDocument::class);
    }
}
