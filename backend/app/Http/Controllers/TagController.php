<?php

namespace App\Http\Controllers;

use App\Models\Enums\PostStatus;
use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a tag with its published articles.
     *
     * This controller was referenced by routes/web.php (route "tags.show")
     * but did not exist, which broke `route:list` / `route:cache`.
     */
    public function show(Tag $tag): View
    {
        $posts = $tag->posts()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('tags.show', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
