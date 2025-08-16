<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'subject', 'note', 'client_id', 'user_agent'];

    public function favoriteCollections()
    {
        return $this->belongsToMany(Collection::class, 'favorites');
    }
}
