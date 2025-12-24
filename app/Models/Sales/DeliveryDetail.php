<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'delivery_detail';
    protected $fillable = [

        'id_pengiriman',
        'id_item',
        'id_satuan',
        'qty_item',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
