<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Enums\PostStatus;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index(): View
    {
        $profile = Profile::query()
            ->with('skills')
            ->first();

        // Diubah menjadi 6 post (2 baris x 3 kolom di desktop)
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
            ->orderBy('name')
            ->get();

        return view('home.index', [
            'profile' => $profile,
            'latestPosts' => $latestPosts,
            'latestProjects' => $latestProjects,
            'categories' => $categories,
        ]);
    }
}