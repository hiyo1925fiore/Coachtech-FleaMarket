<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'exhibition_id',
        'user_id',
        'receiver_id',
        'message',
        'img_url',
        'read_at',
    ];

    protected $dates = ['deleted_at'];

    /**
     * 送信者
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 受信者
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }
}
