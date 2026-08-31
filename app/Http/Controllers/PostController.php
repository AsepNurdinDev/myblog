<?php

namespace App\Http\Controllers;

use App\Models\Enums\CommentStatus;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a paginated list of published articles.
     */
    public function index(): View
    {
        $posts = Post::query()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('posts.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display a single published article.
     *
     * Route model binding resolves the Post by its slug (see routes/web.php).
     * Draft or unpublished posts must not be reachable, so we abort with a
     * 404 instead of leaking their existence.
     */
    public function show(Post $post): View
    {
        abort_unless($post->status === PostStatus::Published, 404);

        $post->load([
            'category',
            'tags',
            'comments' => fn ($query) => $query
                ->where('status', CommentStatus::Approved)
                ->latest(),
        ]);

        return view('posts.show', [
            'post' => $post,
        ]);
    }
}
