<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Billable,HasApiTokens, HasFactory, Notifiable;

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class,'seller_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * このユーザーが他のユーザーに対して行った評価（評価者として）
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    /**
     * このユーザーが他のユーザーから受け取った評価（評価対象として）
     */
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    /**
     * 送信したチャット
     */
    public function sentChats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    /**
     * 受信したチャット
     */
    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    /**
     * このユーザーの平均評価を取得
     */
    public function averageRating()
    {
        return $this->receivedRatings()->avg('rating');
    }

    /**
     * このユーザーの評価数を取得
     */
    public function ratingCount()
    {
        return $this->receivedRatings()->count();
    }

    /**
     * 四捨五入した評価値を取得（0〜5）
     */
    public function getRoundedRating()
    {
        $avg = $this->averageRating();
        if (!$avg) return 0;

        return round($avg);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
