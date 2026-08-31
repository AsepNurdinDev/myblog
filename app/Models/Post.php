<?php

namespace App\Models;

use App\Models\Enums\PostStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'cover_image',
    'status',
    'published_at',
    'seo_title',
    'seo_description',
])]
class Post extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * The category this post belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The tags attached to this post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * The comments left on this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
