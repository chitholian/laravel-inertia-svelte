<?php

namespace App\Models;

use App\Traits\FormatTimeStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory, FormatTimeStamps;

    protected $fillable = ['did', 'trusted', 'last_seen', 'username', 'ip', 'user_agent'];
}
