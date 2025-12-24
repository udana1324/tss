<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTaxInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_tax_invoice';
    protected $fillable = [

        'nomor_faktur',
        'id_seri',
        'id_invoice',
        'dpp',
        'ppn',
        'grand_total',
        'ttl_qty',
        'tanggal_faktur',
        'diskon',
        'flag_export',
        'jenis_faktur',
        'flag_batal',
        'pembetulan',
        'id_parent',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
