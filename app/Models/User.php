<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Micropost;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * このユーザーが所有する投稿。（Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }

    /**
     * このユーザーに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }

    /**
     * このユーザーがフォロー中のユーザー。（Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザーをフォロー中のユーザー。（Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    /**
     * $userIdで指定されたユーザーをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function follow(int $userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            return false;
        } else {
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザーをアンフォローする。
     *
     * @param  int $usereId
     * @return bool
     */
    public function unfollow(int $userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 指定された$userIdのユーザーをこのユーザーがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int $userId
     * @return bool
     */
    public function is_following(int $userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }

    /**
     * このユーザーとフォロー中ユーザーの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザーがフォロー中のユーザーのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザーのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザーが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }

    /**
     * このユーザーのお気に入り。（Micropostモデルとの関係を定義）
     */
    public function favorites()
    {
        // `favorites`テーブルで`user_id`と`micropost_id`が多対多で紐づいている
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }

    /**
     * $micropostIdで指定された投稿をお気に入りにする。
     *
     * @param  int  $micropostId
     * @return bool
     */
    public function favorite(int $micropostId)
    {
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            return false;
        } else {
            $this->favorites()->attach($micropostId);
            return true;
        }
    }

    /**
     * $micropostIdで指定された投稿のお気に入りを解除する。
     *
     * @param  int $usereId
     * @return bool
     */
    public function unfavorite(int $micropostId)
    {
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 指定された$micropostIdの投稿をこのユーザーがお気に入りしているか調べる。お気に入りしているならtrueを返す。
     *
     * @param  int $micropostId
     * @return bool
     */
    public function is_favorite(int $micropostId)
    {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }

}
