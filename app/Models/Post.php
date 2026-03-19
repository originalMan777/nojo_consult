<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'sources',
        'featured_image_path',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_path',
        'noindex',
        'category_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'noindex' => 'boolean',
    ];

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image_path) {
            return null;
        }

        $path = trim($this->featured_image_path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/images/')) {
            return $path;
        }

        if (Str::startsWith($path, 'images/')) {
            return '/' . ltrim($path, '/');
        }

        if (Str::startsWith($path, '/storage/images/')) {
            return Str::replaceFirst('/storage/images/', '/images/', $path);
        }

        if (Str::startsWith($path, 'storage/images/')) {
            return '/' . Str::replaceFirst('storage/images/', 'images/', ltrim($path, '/'));
        }

        if (Str::startsWith($path, '/storage/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
