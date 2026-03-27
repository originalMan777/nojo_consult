use Inertia\Inertia;

Route::get('/blog-test-page', function () {
    return Inertia::render('Blog/Index');
})->name('blog.test.page');
