<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLKasBankDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_kas_bank_detail';
    protected $fillable = [
        'id_kas_bank',
        'id_account',
        'nominal',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
