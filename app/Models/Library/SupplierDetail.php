<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'supplier_detail';
    protected $fillable = [
        'id_supplier',
        'alamat_supplier',
        'kelurahan',
        'kecamatan',
        'kota',
        'kode_pos',
        'jenis_alamat',
        'pic_alamat',
        'telp_pic',
        'default',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
