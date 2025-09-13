<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookVariation extends Model
{
    protected $fillable = [
        'book_id',
        'move',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
