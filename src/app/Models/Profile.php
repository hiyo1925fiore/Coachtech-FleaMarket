<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];


    protected $fillable = [
        'user_id',
        'img_url',
        'post_code',
        'address',
        'building'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 画像URLのアクセサ
     * 画像がない場合はデフォルト画像を返す
     */
    public function getImageUrlAttribute()
    {
        if ($this->img_url) {
            return asset('storage/' . $this->img_url);
        }
        return null;
    }
}
