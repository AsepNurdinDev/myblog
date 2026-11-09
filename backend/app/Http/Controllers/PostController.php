<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::query()
            ->where('status', PostStatus::Published)
            ->with(['category', 'tags'])
            ->latest('published_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Jika opsi 'all' diaktifkan dari tombol, tampilkan seluruh post tanpa batas halaman
        $showAll = $request->boolean('all');
        $posts = $showAll ? $query->get() : $query->paginate(18); // Default 3x6 (18 items)

        $categories = Category::all();

        return view('posts.index', compact('posts', 'categories', 'showAll'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === PostStatus::Published, 404);

        $post->load([
            'category',
            'tags',
            'comments' => fn ($query) => $query->where('status', \App\Models\Enums\CommentStatus::Approved)->latest(),
        ]);

        return view('posts.show', compact('post'));
    }
}