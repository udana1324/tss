<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales';
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
