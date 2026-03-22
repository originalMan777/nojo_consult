<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadBox extends Model
{
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_SERVICE = 'service';
    public const TYPE_OFFER = 'offer';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'type',
        'status',
        'internal_name',
        'title',
        'short_text',
        'button_text',
        'icon_key',
        'content',
        'settings',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class);
    }
}
