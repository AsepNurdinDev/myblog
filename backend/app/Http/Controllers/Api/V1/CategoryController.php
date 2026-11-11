<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ResolvesPerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Category;
use App\Models\Enums\PostStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    use ResolvesPerPage;

    /**
     * All categories, with the number of published articles in each.
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::query()
                ->withCount(['posts' => fn ($q) => $q->where('status', PostStatus::Published)])
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * A category and its paginated published articles.
     *
     * Response: standard paginated "data/links/meta" for the articles plus a
     * top-level "category" object.
     */
    public function show(Request $request, string $slug): AnonymousResourceCollection
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();

        $posts = $category->posts()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->latest('id')
            ->paginate($this->perPage($request, 10))
            ->withQueryString();

        return PostResource::collection($posts)->additional([
            'category' => (new CategoryResource($category))->resolve(),
        ]);
    }
}
