<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait HasFormattedDates
{
    /**
     * Trả về ngày cập nhật (updated_at) theo định dạng d-m-Y.
     * Nếu null thì trả về chuỗi rỗng.
     */
    public function getUpdatedDate(): string
    {
        if (!$this->updated_at) {
            return '';
        }

        // Bảo đảm dùng timezone app
        $dt = $this->updated_at instanceof Carbon
            ? $this->updated_at
            : Carbon::parse($this->updated_at);

        return $dt->timezone(config('app.timezone'))->format('d-m-Y');
    }

    /**
     * (Tuỳ chọn) Trả về ngày tạo theo định dạng d-m-Y.
     */
    public function getCreatedDate(): string
    {
        if (!$this->created_at) {
            return '';
        }

        $dt = $this->created_at instanceof Carbon
            ? $this->created_at
            : Carbon::parse($this->created_at);

        return $dt->timezone(config('app.timezone'))->format('d-m-Y');
    }
}
