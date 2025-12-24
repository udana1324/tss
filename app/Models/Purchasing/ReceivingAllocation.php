<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivingAllocation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'receiving_allocation';
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
