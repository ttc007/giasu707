<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

trait HasLikesCount
{
    public function countLikes()
    {
        $modelType = class_basename($this); // Ví dụ: 'Collection'
        
        return DB::table('favorites')
            ->where('model_type', $modelType)
            ->where('model_id', $this->id)
            ->count();
    }
}
