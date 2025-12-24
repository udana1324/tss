<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchase_order_detail';
    protected $fillable = [

        'id_po',
        'id_item',
        'id_satuan',
        'qty_order',
        'outstanding_qty',
        'harga_beli',
        'created_by',
        'updated_by',
        'deleted_by'

    ];
}
