<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleAccess extends Model
{
    use HasFactory;

    protected $table = 'module_access';
    protected $fillable = [

        'user_id',
        'menu_id',
        'add',
        'edit',
        'delete',
        'posting',
        'print',
        'export',
        'approve',
        'revisi',
        'active',
        'created_by',
        'updated_by',
        'deleted_by',

    ];
}
