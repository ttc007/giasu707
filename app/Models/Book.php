<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'image_chess',
        'color',
        'move',
        'comment',
        'opening_id',
        'step',
        'book_variation_id',
        'is_hidden'
    ];

    // Một book có nhiều biến thể
    public function variations()
    {
        return $this->hasMany(BookVariation::class, 'book_id');
    }
}
