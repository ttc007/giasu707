<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasViewsCount;
use App\Models\Traits\HasLikesCount;

class Collection extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'slug'
    ];

    use HasViewsCount;
    use HasLikesCount;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
