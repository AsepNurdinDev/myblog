<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Project;
use Illuminate\Http\Request;

/**
 * Full project representation, including the long-form HTML content.
 *
 * @mixin Project
 */
class ProjectDetailResource extends ProjectResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'content' => $this->content,
        ];
    }
}
