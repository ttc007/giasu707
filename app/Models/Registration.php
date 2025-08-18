<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'name', 
        'phone', 
        'email', 
        'subject', 
        'note', 
        'ip_address', 
        'user_agent', 
        'password',
        'is_active',
        'activation_key'
    ];

    public function favoriteCollections()
    {
        return $this->belongsToMany(Collection::class, 'favorites');
    }
}
