<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'module';
    protected $fillable = [
        'menu',
        'url',
        'parent',
        'order_number',
        'menu_icon',
        'active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
