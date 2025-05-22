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
        'image',
    ];

    public function isPurchased(){
        //purchasesテーブルに同じexhibition_idがあるかチェック
        return $this->purchase()->exists();
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
}
