<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Models\Profile;

class ProfileController extends Controller
{
    /**
     * The single public profile of the blog owner, with skills.
     */
    public function show(): ProfileResource
    {
        $profile = Profile::query()
            ->with(['user:id,name', 'skills'])
            ->firstOrFail();

        return new ProfileResource($profile);
    }
}
