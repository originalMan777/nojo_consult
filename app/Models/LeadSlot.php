<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadSlot extends Model
{
    protected $fillable = [
        'key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function assignment(): HasOne
    {
        return $this->hasOne(LeadAssignment::class);
    }
}
