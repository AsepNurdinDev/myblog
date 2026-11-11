<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Project;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Project summary used in listings.
 *
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => Media::url($this->thumbnail),
            'url' => $this->url,
            'github_url' => $this->github_url,
            'status' => $this->status?->value,
            'started_at' => $this->started_at?->toDateString(),
            'completed_at' => $this->completed_at?->toDateString(),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
        ];
    }
}
