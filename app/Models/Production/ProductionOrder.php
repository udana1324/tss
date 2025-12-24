<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_order';
    protected $fillable = [

        'no_production_order',
        'po_production',
        'id_supplier',
        'id_alamat',
        'jumlah_total',
        'outstanding_qty',
        'tanggal',
        'tanggal_request',
        'tanggal_deadline',
        'nominal_po_dpp',
        'nominal_po_ppn',
        'nominal_po_ttl',
        'jenis_diskon',
        'persentase_diskon',
        'nominal_diskon',
        'flag_ppn',
        'metode_pembayaran',
        'durasi_jt',
        'status_po',
        'flag_revisi',
        'flag_internal',
        'jumlah_revisi',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
