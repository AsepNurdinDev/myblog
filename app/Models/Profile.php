<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'user_id',
    'avatar',
    'headline',
    'bio',
    'location',
    'website',
    'github',
    'linkedin',
    'instagram',
    'twitter',
])]
class Profile extends Model
{
    /**
     * Accessor untuk mengambil teks bio bersih tanpa tag HTML.
     */
    protected function cleanBio(): Attribute
    {
        return Attribute::make(
            get: fn () => strip_tags((string) $this->bio),
        );
    }

    /**
     * The user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The skills mastered by this profile.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }
}