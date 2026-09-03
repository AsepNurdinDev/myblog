<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Enums\PostStatus;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a category with its published articles.
     */
    public function show(Category $category): View
    {
        $posts = $category->posts()
            ->where('status', PostStatus::Published)
            ->with('tags')
            ->latest('published_at')
            ->paginate(10);

        // Ambil semua daftar kategori untuk navigasi/filter di view
        $categories = Category::all();

        return view('categories.show', [
            'category'   => $category,
            'posts'      => $posts,
            'categories' => $categories,
        ]);
    }
}