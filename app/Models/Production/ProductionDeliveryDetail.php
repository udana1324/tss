<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionDeliveryDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_delivery_detail';

    protected $fillable = [

        'id_delivery',
        'id_item',
        'id_satuan',
        'qty_item',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'

    ];
}
