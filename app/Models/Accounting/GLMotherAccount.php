<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLMotherAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_mother_account';
    protected $fillable = [
        'account_name',
        'account_number',
        'default_side',
        'order_number',
        'group',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
