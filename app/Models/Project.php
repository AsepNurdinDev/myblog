<?php

namespace App\Models;

use App\Models\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'title',
    'slug',
    'description',
    'content',
    'thumbnail',
    'url',
    'github_url',
    'status',
    'started_at',
    'completed_at',
])]
class Project extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'started_at' => 'date',
            'completed_at' => 'date',
        ];
    }

    /**
     * The skills/technologies used to build this project.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }
}
