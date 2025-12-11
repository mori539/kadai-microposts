<?php

use App\Models\Micropost;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\MicropostsController;
use App\Http\Controllers\UserFollowController;

Route::get('/', [MicropostsController::class, 'index'])->name('home');

Route::get('/dashboard', [MicropostsController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::prefix('users/{id}')->group(function () {
        // フォローする
        Route::post('follow', [UserFollowController::class, 'store'])->name('user.follow');
        // フォロー解除する
        Route::delete('unfollow', [UserFollowController::class, 'destroy'])->name('user.unfollow');
        // フォロイーのユーザー
        Route::get('followings', [UsersController::class, 'followings'])->name('users.followings');
        // フォロワーのユーザー
        Route::get('followers', [UsersController::class, 'followers'])->name('users.followers');

        // お気に入りにする
        Route::post('favorite', [FavoritesController::class, 'store'])->name('micropost.favorite');
        // お気に入り解除する
        Route::delete('unfavorite', [FavoritesController::class, 'destroy'])->name('micropost.unfavorite');
        // お気に入りの投稿一覧
        Route::get('favorites', [UsersController::class, 'favorites'])->name('user.favoriteList');

    });

    Route::resource('users', UsersController::class, ['only' => ['index', 'show']]);
    Route::resource('microposts', MicropostsController::class, ['only' => ['store', 'destroy']]);
});

require __DIR__.'/auth.php';
