<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiController;

Route::get('/', [ApiController::class, 'index'])->name('api.index');
Route::get('/blogs', [ApiController::class, 'blogs'])->name('api.blogs');
Route::get('/categories', [ApiController::class, 'categories'])->name('api.categories');
Route::get('/tags', [ApiController::class, 'tags'])->name('api.tags');
Route::get('/topics', [ApiController::class, 'topics'])->name('api.topics');
Route::get('/menu-items', [ApiController::class, 'menuItems'])->name('api.menu.items');
Route::get('/settings', [ApiController::class, 'settings'])->name('api.settings');
Route::get('/page/{slug}', [ApiController::class, 'pages'])->name('api.page');
Route::get('/blog/{slug}', [ApiController::class, 'blog'])->name('api.blog');
Route::get('/author/{displayName}', [ApiController::class, 'user'])->name('api.author');
Route::get('/all-companies', [ApiController::class, 'allCompanies'])->name('api.all.companies');
Route::get('/companies', [ApiController::class, 'companies'])->name('api.companies');
Route::get('/company/{slug}', [ApiController::class, 'company'])->name('api.company');
Route::get('/category/{category}', [ApiController::class, 'category'])->name('api.category');
Route::post('/blog-comment  ', [ApiController::class, 'blogPostComments'])->name('api.blog.comment');
Route::get('/blog-comments', [ApiController::class, 'blogComments'])->name('api.blog.comments');
Route::get('/latest-blogs', [ApiController::class, 'latestBlogs'])->name('api.latest.blogs');
Route::post('/company-review', [ApiController::class, 'companyReviews'])->name('api.company.review');
Route::get('/company-reviews', [ApiController::class, 'companyReviews'])->name('api.company.reviews');
