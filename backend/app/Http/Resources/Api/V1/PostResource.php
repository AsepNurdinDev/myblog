<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Article summary used in listings (no body content, no comments).
 *
 * @mixin Post
 */
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'cover_image' => Media::url($this->cover_image),
            'published_at' => $this->published_at?->utc()->toIso8601ZuluString(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
