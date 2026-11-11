<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public REST API (v1)
|--------------------------------------------------------------------------
|
| Consumed by the Next.js frontend. Every route is public and stateless:
| no sessions, no CSRF, no Sanctum. The "api" prefix is applied by
| bootstrap/app.php, so these routes live under /api/v1.
|
*/

Route::prefix('v1')->name('api.v1.')->middleware('throttle:api')->group(function () {
    Route::get('home', HomeController::class)->name('home');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
    Route::post('posts/{slug}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:comments')
        ->name('posts.comments.store');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/{slug}', [TagController::class, 'show'])->name('tags.show');

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
});
