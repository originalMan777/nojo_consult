<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'lead_box_id',
        'lead_slot_key',
        'page_key',
        'source_url',
        'type',
        'first_name',
        'email',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function leadBox(): BelongsTo
    {
        return $this->belongsTo(LeadBox::class);
    }
}
