<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderInternalDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_order_internal_detail';
    protected $fillable = [

        'id_so',
        'id_item',
        'id_satuan',
        'qty_item',
        'qty_outstanding',
        'qty_order',
        'harga_jual',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
