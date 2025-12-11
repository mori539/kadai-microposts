<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    /**
     * 投稿をお気に入りするアクション。
     *
     * @param  $id  投稿id
     * @return \Illuminate\Http\Response
     */
    public function store(string $id)
    {
        // 認証済みユーザー（閲覧者）が、 idのユーザーをフォローする
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->favorite(intval($id));
        // 前のURLへリダイレクトさせる
        return back();
    }

    /**
     * 投稿のお気に入りを解除するアクション。
     *
     * @param  $id  投稿id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        // 認証済みユーザー（閲覧者）が、 idのユーザーをアンフォローする
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->unfavorite(intval($id));
        // 前のURLへリダイレクトさせる
        return back();
    }
}
