<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category_id',
        'collection_id',
        'slug'
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function next()
    {
        return Post::where('collection_id', $this->collection_id)
                     ->where('id', '>', $this->id)
                     ->orderBy('id', 'asc')
                     ->first(); // không dùng findOrFail để tránh lỗi
    }

    public function prev()
    {
        return Post::where('collection_id', $this->collection_id)
                     ->where('id', '<', $this->id)
                     ->orderBy('id', 'desc')
                     ->first();
    }
}
