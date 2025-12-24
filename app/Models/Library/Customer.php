<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customer';
    protected $fillable = [
        'kode_customer',
        'nama_customer',
        'npwp_customer',
        'ktp_customer',
        'telp_customer',
        'fax_customer',
        'email_customer',
        'kategori_customer',
        'jenis_customer',
        'limit_customer',
        'id_account',
        'sales',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
