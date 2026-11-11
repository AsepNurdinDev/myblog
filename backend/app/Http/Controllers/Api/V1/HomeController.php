<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Category;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Homepage payload. Mirrors the Blade HomeController: profile, the latest
     * 6 published articles, the latest 3 projects and all categories.
     */
    public function __invoke(): JsonResponse
    {
        $profile = Profile::query()->with(['user:id,name', 'skills'])->first();

        $latestPosts = Post::query()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $latestProjects = Project::query()
            ->with('skills')
            ->latest('started_at')
            ->take(3)
            ->get();

        $categories = Category::query()
            ->withCount(['posts' => fn ($q) => $q->where('status', PostStatus::Published)])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => [
                'profile' => $profile ? (new ProfileResource($profile))->resolve() : null,
                'latest_posts' => PostResource::collection($latestPosts)->resolve(),
                'latest_projects' => ProjectResource::collection($latestProjects)->resolve(),
                'categories' => CategoryResource::collection($categories)->resolve(),
            ],
        ]);
    }
}
