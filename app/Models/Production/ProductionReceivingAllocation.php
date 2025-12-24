<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionReceivingAllocation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_receiving_allocation';
    protected $fillable = [

        'id_receiving',
        'id_detail',
        'id_item',
        'id_satuan',
        'qty_item',
        'id_index',
        'created_by',
        'updated_by',
        'deleted_by',

    ];
}
