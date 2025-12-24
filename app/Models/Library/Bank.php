<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'bank';
    protected $fillable = [
        'kode_bank',
        'nama_bank',
        'deskripsi_bank',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
