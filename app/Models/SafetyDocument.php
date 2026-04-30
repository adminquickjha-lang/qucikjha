<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyDocument extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'project_name',
        'project_location',
        'project_description',
        'equipment_tools',
        'prepared_by',
        'competent_person',
        'safety_coordinator',
        'regulations',
        'document_type',
        'ai_response',
        'logo_path',
        'download_ready',
        'is_paid',
        'stripe_session_id',
        'amount',
        'input_tokens',
        'output_tokens',
        'cost',
    ];

    protected $casts = [
        'regulations' => 'array',
        'ai_response' => 'array',
        'download_ready' => 'boolean',
        'is_paid' => 'boolean',
        'amount' => 'decimal:2',
        'cost' => 'decimal:6',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professionalReviews()
    {
        return $this->hasMany(ProfessionalReview::class);
    }

    public function reviews()
    {
        return $this->hasMany(DocumentReview::class);
    }

    /**
     * Get the total input tokens spent on this document (Generation + Reviews).
     */
    public function getTotalInputTokensAttribute(): int
    {
        return $this->input_tokens + $this->reviews()->sum('input_tokens');
    }

    /**
     * Get the total output tokens spent on this document (Generation + Reviews).
     */
    public function getTotalOutputTokensAttribute(): int
    {
        return $this->output_tokens + $this->reviews()->sum('output_tokens');
    }

    /**
     * Get the total tokens (Input + Output) spent on this document.
     */
    public function getTotalTokensAttribute(): int
    {
        return $this->total_input_tokens + $this->total_output_tokens;
    }

    /**
     * Get the total AI cost spent on this document (Generation + Reviews).
     */
    public function getTotalAiCostAttribute(): float
    {
        return (float) ($this->cost + $this->reviews()->sum('cost'));
    }
}
