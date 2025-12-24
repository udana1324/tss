<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchase_order';
    protected $fillable = [

        'no_po',
        'id_supplier',
        'id_alamat',
        'jumlah_total_po',
        'outstanding_po',
        'tanggal_po',
        'tanggal_request',
        'tanggal_deadline',
        'nominal_po_dpp',
        'nominal_po_ppn',
        'nominal_po_ttl',
        'jenis_diskon',
        'persentase_diskon',
        'nominal_diskon',
        'flag_ppn',
        'ppn',
        'metode_pembayaran',
        'durasi_jt',
        'status_po',
        'flag_revisi',
        'jumlah_revisi',
        'id_ppn',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
