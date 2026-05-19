<?php

use App\Http\Controllers\CoinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Models\Article;

/*
|--------------------------------------------------------------------------
| PUBLIC APP ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| COIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('coin/{symbol}')->group(function () {
    Route::get('/news', [HomeController::class, 'coinNews'])->name('coin.news');
    Route::get('/distribution', [HomeController::class, 'coinDistribution'])->name('coin.distribution');
    Route::get('/chart', [HomeController::class, 'coinChart'])->name('coin.chart');
});

/*
|--------------------------------------------------------------------------
| TRENDING
|--------------------------------------------------------------------------
*/

Route::get('/trending', [HomeController::class, 'trendingNews'])->name('trending.news');
Route::get('/trending/filter', [HomeController::class, 'trendingFilter'])->name('trending.filter');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE (AUTH)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    /* Test admin middleware */
    Route::get('/admin-test', function () {
        return "ADMIN OK";
    });

    /*
    |--------------------------------------------------------------------------
    | COIN MANAGEMENT (FULL CRUD)
    |--------------------------------------------------------------------------
    */
    Route::resource('coins', CoinController::class);

    /*
    |--------------------------------------------------------------------------
    | ARTICLE MANAGEMENT
    |--------------------------------------------------------------------------
    */
    Route::delete('/articles/{id}', function ($id) {
        $article = Article::findOrFail($id);
        $article->delete();
        return back();
    })->name('admin.article.delete');

    /*
    |--------------------------------------------------------------------------
    | TRENDING CONTROLS
    |--------------------------------------------------------------------------
    */
    Route::patch('/trending/pin/{id}', function ($id) {
        $article = Article::findOrFail($id);
        $article->is_pinned = !$article->is_pinned;
        $article->save();
        return back();
    })->name('admin.trending.pin');

    Route::patch('/trending/hide/{id}', function ($id) {
        $article = Article::findOrFail($id);
        $article->hidden_from_trending = !$article->hidden_from_trending;
        $article->save();
        return back();
    })->name('admin.trending.hide');

});
