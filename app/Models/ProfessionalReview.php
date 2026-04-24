<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalReview extends Model
{
    protected $fillable = [
        'user_id',
        'safety_document_id',
        'message',
        'token',
        'is_paid',
        'stripe_session_id',
        'progress',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function safetyDocument()
    {
        return $this->belongsTo(SafetyDocument::class);
    }
}
