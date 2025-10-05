<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'seller_id',
        'condition_id',
        'name',
        'brand',
        'price',
        'description',
        'img_url',
    ];

    public function isPurchased()
    {
        //purchasesテーブルに同じexhibition_idがあるかチェック
        return $this->purchase()->exists();
    }

    /**
     * 未読メッセージがあるかチェック
     * @param int|null $userId 受信者のユーザーID（nullの場合は現在のログインユーザー）
     */
    public function isUnread($userId = null)
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        return $this->chats()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->exists();
    }

    /**
     * 商品の未読メッセージ数を取得
     * @param int|null $userId 受信者のユーザーID（nullの場合は現在のログインユーザー）
     */
    public function getUnreadCount($userId = null)
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        return $this->chats()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * 取引中の商品かどうか判定
     * 購入済みで、かつログインユーザーが評価を完了していない商品
     */
    public function isTrading($userId = null)
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        // 購入されていない場合は取引中ではない
        if (!$this->isPurchased()) {
            return false;
        }

        // ログインユーザーがこの商品の評価をしていない場合は取引中
        return !$this->ratings()
            ->where('rater_id', $userId)
            ->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'seller_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_exhibition', 'exhibition_id', 'category_id')->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'exhibition_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'exhibition_id');
    }
}
