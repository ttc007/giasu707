<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

trait HasViewsCount
{
    public function countView()
    {
        $modelType = class_basename($this); // Lấy tên class, vd: 'Post'
        return DB::table('views')
            ->where('model_type', $modelType)
            ->where('model_id', $this->id)
            ->count();
    }
}
