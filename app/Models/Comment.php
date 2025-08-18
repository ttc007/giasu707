<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasFormattedDates;

class Comment extends Model
{
    protected $fillable = [
        'registration_id',
        'content',
        'model_id',
        'model_type'
    ];

    use HasFormattedDates;

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
