<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ResolvesPerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Enums\PostStatus;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    use ResolvesPerPage;

    /**
     * All tags, with the number of published articles using each.
     */
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection(
            Tag::query()
                ->withCount(['posts' => fn ($q) => $q->where('status', PostStatus::Published)])
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * A tag and its paginated published articles.
     *
     * Response: standard paginated "data/links/meta" for the articles plus a
     * top-level "tag" object.
     */
    public function show(Request $request, string $slug): AnonymousResourceCollection
    {
        $tag = Tag::query()->where('slug', $slug)->firstOrFail();

        $posts = $tag->posts()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->latest('posts.id')
            ->paginate($this->perPage($request, 10))
            ->withQueryString();

        return PostResource::collection($posts)->additional([
            'tag' => (new TagResource($tag))->resolve(),
        ]);
    }
}
