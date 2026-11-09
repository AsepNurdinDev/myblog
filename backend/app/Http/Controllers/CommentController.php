<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Enums\CommentStatus;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a guest comment on a published article.
     *
     * The target Post is resolved via route model binding on its slug
     * (see routes/web.php) rather than trusting a post_id from the
     * request body. Comments are never allowed on unpublished posts,
     * and every new comment is created as "pending" so it goes through
     * moderation in Filament before it becomes visible.
     */
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        abort_unless($post->status === PostStatus::Published, 404);

        $post->comments()->create([
            ...$request->validated(),
            'status' => CommentStatus::Pending,
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'comment-submitted');
    }
}
