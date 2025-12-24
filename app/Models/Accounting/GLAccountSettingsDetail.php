<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLAccountSettingsDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_account_settings_detail';
    protected $fillable = [
        'id_settings',
        'id_account',
        'side',
        'module_source',
        'field_source',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
