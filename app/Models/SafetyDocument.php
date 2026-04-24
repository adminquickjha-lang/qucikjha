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
    ];

    protected $casts = [
        'regulations' => 'array',
        'ai_response' => 'array',
        'download_ready' => 'boolean',
        'is_paid' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professionalReviews()
    {
        return $this->hasMany(ProfessionalReview::class);
    }
}
