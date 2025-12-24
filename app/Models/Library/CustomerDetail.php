<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customer_detail';
    protected $fillable = [
        'id_customer',
        'nama_outlet',
        'alamat_customer',
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
