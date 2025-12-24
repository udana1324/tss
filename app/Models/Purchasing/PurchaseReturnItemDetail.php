<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnItemDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchase_return_item_detail';
    protected $fillable = [

        'id_retur',
        'id_item',
        'id_satuan',
        'qty_item',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
