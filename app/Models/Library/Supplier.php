<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'supplier';
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'npwp_supplier',
        'telp_supplier',
        'fax_supplier',
        'email_supplier',
        'kategori_supplier',
        'id_account',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
