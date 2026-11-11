<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public representation of a comment.
 *
 * The commenter's email address is deliberately NOT part of this resource.
 *
 * @mixin Comment
 */
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'created_at' => $this->created_at?->utc()->toIso8601ZuluString(),
        ];
    }
}
