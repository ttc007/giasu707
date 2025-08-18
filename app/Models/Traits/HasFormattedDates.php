<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait HasFormattedDates
{
    /**
     * Trả về ngày cập nhật theo dạng "x ngày trước".
     */
    public function getUpdatedDate(): string
    {
        if (!$this->updated_at) {
            return '';
        }

        $dt = $this->updated_at instanceof Carbon
            ? $this->updated_at
            : Carbon::parse($this->updated_at);

        return $dt->timezone(config('app.timezone'))->diffForHumans();
    }

    /**
     * Trả về ngày tạo theo dạng "x ngày trước".
     */
    public function getCreatedDate(): string
    {
        if (!$this->created_at) {
            return '';
        }

        $dt = $this->created_at instanceof Carbon
            ? $this->created_at
            : Carbon::parse($this->created_at);

        return $dt->timezone(config('app.timezone'))->diffForHumans();
    }
}
