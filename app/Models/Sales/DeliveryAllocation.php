<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAllocation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'delivery_allocation';
    protected $fillable = [

        'id_delivery',
        'id_detail',
        'id_item',
        'qty_item',
        'qty_dus',
        'id_index',
        'created_by',
        'updated_by',
        'deleted_by',

    ];
}
