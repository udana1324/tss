<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'delivery';
    protected $fillable = [

        'kode_pengiriman',
        'no_sj_manual',
        'id_so',
        'id_alamat',
        'jumlah_total_sj',
        'tanggal_sj',
        'tanggal_kirim',
        'metode_pengiriman',
        'status_pengiriman',
        'flag_revisi',
        'flag_terms_so',
        'flag_invoiced',
        'flag_terkirim',
        'diterima_oleh',
        'tanggal_diterima',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
