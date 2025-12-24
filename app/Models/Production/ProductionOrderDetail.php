<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_order_detail';
    protected $fillable = [

        'id_po',
        'id_item',
        'id_satuan',
        'qty_order',
        'outstanding_qty',
        'harga',
        'created_by',
        'updated_by',
        'deleted_by'

    ];
}
