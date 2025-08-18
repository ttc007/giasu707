<?php

namespace App\Models\Traits;

use App\Models\Comment;

trait GetComments
{
    /**
     * Lấy toàn bộ comments của model hiện tại kèm phân trang
     */
    public function commentsPaginate($perPage = 10)
    {
        $modelType = class_basename($this);

        return \App\Models\Comment::where('model_type', $modelType)
            ->where('model_id', $this->id)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Lấy danh sách comment mới nhất kèm phân trang
     */
    public function latestCommentsPaginate($perPage = 10)
    {
        $modelType = class_basename($this);

        return \App\Models\Comment::where('model_type', $modelType)
            ->where('model_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Đếm tổng số comments của model hiện tại
     */
    public function commentsCount()
    {
        $modelType = class_basename($this);

        return Comment::where('model_type', $modelType)
            ->where('model_id', $this->id)
            ->count();
    }
}

