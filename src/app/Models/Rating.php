<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'user_id',       // 評価対象ユーザー（取引相手）
        'rater_id',      // 評価者
        'exhibition_id',
        'rating',        // 評価点（1〜5）
    ];

    /**
     * 評価対象のユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 評価者
     */
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    /**
     * 評価対象の商品
     */
    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }
}
