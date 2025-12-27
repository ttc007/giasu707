<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoveStat extends Model
{
    protected $fillable = [
        'board',
        'turn',
        'move_text',
        'win_count',
        'lose_count',
        'draw_count'
    ];
}
