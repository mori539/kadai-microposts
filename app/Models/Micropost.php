<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Micropost extends Model
{
    /** @use HasFactory<\Database\Factories\MicropostFactory> */
    use HasFactory;


    protected $fillable = ['content'];


    /**
     * この投稿を所有するユーザー。（Userモデルとの関係を定義）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
