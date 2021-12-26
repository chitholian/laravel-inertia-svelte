<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatTimeStamps
{
    public function getCreatedAtAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
    }

    public function getLastSeenAttribute($value)
    {
        return $value ? Carbon::parse($value)->diffForHumans() : '';
    }

    public function getLogoutTimeAttribute($value)
    {
        return $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
    }
}
