<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLAccountLevel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_account_level';
    protected $fillable = [
        'nama_level',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
