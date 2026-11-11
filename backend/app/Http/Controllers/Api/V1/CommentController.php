<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePostCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Comment;
use App\Models\Enums\CommentStatus;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    private const SUCCESS_MESSAGE = 'Thank you! Your comment has been submitted and will appear after it is approved.';

    /**
     * Store a guest comment on a published article.
     *
     * - No authentication; the post is resolved by slug and must be published.
     * - Every comment is stored as "pending" and needs admin approval.
     * - Honeypot hits are answered with a normal-looking success but not stored.
     * - An identical pending comment (same post, email and content) sent in the
     *   last 10 minutes is not stored twice, which makes double-clicks harmless.
     */
    public function store(StorePostCommentRequest $request, string $slug): JsonResponse
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->firstOrFail();

        if ($request->isHoneypotTriggered()) {
            return $this->accepted(null);
        }

        $data = $request->validated();

        $duplicate = $post->comments()
            ->where('email', $data['email'])
            ->where('content', $data['content'])
            ->where('status', CommentStatus::Pending)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if ($duplicate) {
            return $this->accepted($duplicate);
        }

        $comment = $post->comments()->create([
            ...$data,
            'status' => CommentStatus::Pending,
        ]);

        return $this->accepted($comment);
    }

    private function accepted(?Comment $comment): JsonResponse
    {
        return response()->json([
            'message' => self::SUCCESS_MESSAGE,
            'status' => CommentStatus::Pending->value,
            'data' => $comment ? (new CommentResource($comment))->resolve() : null,
        ], 201);
    }
}
