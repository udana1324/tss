<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTaxInvoiceDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_tax_invoice_detail';
    protected $fillable = [

        'id_faktur',
        'id_item',
        'id_satuan',
        'harga_jual',
        'qty',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
