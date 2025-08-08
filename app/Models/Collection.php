<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'slug'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favoriteCount(): int
    {
        return \DB::table('favorites')
            ->where('collection_id', $this->id)
            ->count();
    }
}
