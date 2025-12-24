<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoiceDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_invoice_detail';
    protected $fillable = [

        'id_invoice',
        'id_sj',
        'qty_sj',
        'subtotal_sj',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
