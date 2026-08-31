<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes
|--------------------------------------------------------------------------
|
| These routes serve the public-facing blog. There is no authentication
| here — admin-only access is handled separately by Filament.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/articles', [PostController::class, 'index'])->name('posts.index');
Route::get('/articles/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::post('/articles/{post:slug}/comments', [CommentController::class, 'store'])->name('comments.store');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/about', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');