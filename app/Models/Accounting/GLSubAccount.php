<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLSubAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_sub_account';
    protected $fillable = [
        'account_name',
        'account_number',
        'id_mother_account',
        'id_account',
        'order_number',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
