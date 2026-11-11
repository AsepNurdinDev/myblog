<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Profile;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public profile. Only the display name of the owner is exposed; the user's
 * email, password and any other account data are never serialized.
 *
 * @mixin Profile
 */
class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->user?->name,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'bio_text' => $this->clean_bio,
            'location' => $this->location,
            'avatar' => Media::url($this->avatar),
            'website' => $this->website,
            'github' => $this->github,
            'linkedin' => $this->linkedin,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
        ];
    }
}
