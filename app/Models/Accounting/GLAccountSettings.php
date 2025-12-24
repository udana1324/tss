<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLAccountSettings extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_account_settings';
    // protected $fillable = [
    //     'module',
    //     'created_by',
    //     'updated_by',
    //     'deleted_by'
    // ];
}
