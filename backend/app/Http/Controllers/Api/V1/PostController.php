<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ResolvesPerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostDetailResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Enums\CommentStatus;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    use ResolvesPerPage;

    /**
     * Paginated list of published articles.
     *
     * Query: search, category (slug), tag (slug), per_page (1-50), page.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Post::query()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->latest('id');

        if (is_string($search = $request->query('search')) && trim($search) !== '') {
            // "!" is the escape character: portable across MySQL and SQLite.
            $term = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], trim($search)).'%';

            $query->where(function ($q) use ($term) {
                $q->whereRaw("title LIKE ? ESCAPE '!'", [$term])
                    ->orWhereRaw("excerpt LIKE ? ESCAPE '!'", [$term]);
            });
        }

        if (is_string($category = $request->query('category')) && $category !== '') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        if (is_string($tag = $request->query('tag')) && $tag !== '') {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
        }

        return PostResource::collection(
            $query->paginate($this->perPage($request))->withQueryString()
        );
    }

    /**
     * A single published article. Drafts return 404.
     */
    public function show(string $slug): PostDetailResource
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->withCount([
                'comments as approved_comments_count' => fn ($q) =>
                    $q->where('status', CommentStatus::Approved),
            ])
            ->with([
                'category',
                'tags',
                'comments' => fn ($q) => $q
                    ->where('status', CommentStatus::Approved)
                    ->latest()
                    ->latest('id')
                    ->limit(50),
            ])
            ->firstOrFail();

        return new PostDetailResource($post);
    }
}
