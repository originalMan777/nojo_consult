<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogIndexSection extends Model
{
    use HasFactory;

    public const KEY_WIDE = 'wide_section';
    public const KEY_CLUSTER_LEFT = 'cluster_left';
    public const KEY_CLUSTER_RIGHT = 'cluster_right';

    public const SOURCE_LATEST = 'latest';
    public const SOURCE_FEATURED = 'featured';
    public const SOURCE_CATEGORY = 'category';

    public const SECTION_KEYS = [
        self::KEY_WIDE,
        self::KEY_CLUSTER_LEFT,
        self::KEY_CLUSTER_RIGHT,
    ];

    public const SOURCE_TYPES = [
        self::SOURCE_LATEST,
        self::SOURCE_FEATURED,
        self::SOURCE_CATEGORY,
    ];

    protected $fillable = [
        'section_key',
        'enabled',
        'source_type',
        'category_id',
        'title_override',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
