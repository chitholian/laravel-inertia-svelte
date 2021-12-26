<?php

namespace App\Models;

use App\Traits\FormatTimeStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginReport extends Model
{
    use HasFactory, FormatTimeStamps;

    protected $fillable = ['user_id', 'username', 'user_agent', 'ip', 'did'];
}
