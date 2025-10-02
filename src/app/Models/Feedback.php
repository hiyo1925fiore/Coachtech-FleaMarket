<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'exhibition_id',
        'feedback',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }
}
