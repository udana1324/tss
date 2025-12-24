<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preference extends Model
{
    use SoftDeletes;
    protected $table = 'preference';
    protected $fillable = [
        'direktur',
        'nama_pt',
        'alamat_pt',
        'kelurahan_pt',
        'kecamatan_pt',
        'kota_pt',
        'npwp_pt',
        'npwp_pt_16',
        'rekening',
        'telp_pt',
        'email_pt',
        'website_pt',
        'flag_default',
        'flag_do',
        'flag_rcv',
        'flag_quo',
        'flag_so',
        'flag_po',
        'flag_inv_sale',
        'flag_inv_purc',
        'flag_inv_dp',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
