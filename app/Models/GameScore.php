<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
     protected $fillable = [
        'user_id',
        'user_name',
        'score'
    ];
}
