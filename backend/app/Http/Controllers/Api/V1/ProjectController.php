<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ResolvesPerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectDetailResource;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    use ResolvesPerPage;

    /**
     * Paginated projects.
     *
     * Preserves the existing visibility rule of the Blade site: ProjectStatus
     * has no "hidden/draft" case, so every project (ongoing, completed or
     * archived) is public.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            Project::query()
                ->with('skills')
                ->latest('started_at')
                ->latest('id')
                ->paginate($this->perPage($request, 9))
                ->withQueryString()
        );
    }

    public function show(string $slug): ProjectDetailResource
    {
        $project = Project::query()
            ->where('slug', $slug)
            ->with('skills')
            ->firstOrFail();

        return new ProjectDetailResource($project);
    }
}
