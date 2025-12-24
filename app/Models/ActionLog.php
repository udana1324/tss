<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    use HasFactory;
    protected $table = 'action_log';
    protected $fillable = [
        'module',
        'action',
        'desc',
        'username'
    ];
}
