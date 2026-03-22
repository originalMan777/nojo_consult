<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAssignment extends Model
{
    protected $fillable = [
        'lead_slot_id',
        'lead_box_id',
        'override_title',
        'override_short_text',
        'override_button_text',
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(LeadSlot::class, 'lead_slot_id');
    }

    public function leadBox(): BelongsTo
    {
        return $this->belongsTo(LeadBox::class, 'lead_box_id');
    }
}
