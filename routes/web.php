<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Public\PostController as PublicPostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/about', fn () => Inertia::render('About'))->name('about');
Route::get('/services', fn () => Inertia::render('Services'))->name('services');
Route::get('/consultation', fn () => Inertia::render('Consultation'))->name('consultation');
Route::get('/resources', fn () => Inertia::render('Resources'))->name('resources');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

Route::get('/blog', [PublicPostController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [PublicPostController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [PublicPostController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [PublicPostController::class, 'show'])->name('blog.show');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::redirect('/dashboard', '/admin/posts')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}', [AdminPostController::class, 'show'])->name('posts.show');
        Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/{post}/publish', [AdminPostController::class, 'publish'])->name('posts.publish');
        Route::post('/posts/{post}/unpublish', [AdminPostController::class, 'unpublish'])->name('posts.unpublish');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

        Route::get('/media', [MediaLibraryController::class, 'index'])->name('media.index');
        Route::get('/media/browser', [MediaLibraryController::class, 'browser'])->name('media.browser');
        Route::post('/media', [MediaLibraryController::class, 'store'])->name('media.store');
        Route::delete('/media', [MediaLibraryController::class, 'destroy'])->name('media.destroy');
    });

require __DIR__.'/settings.php';
