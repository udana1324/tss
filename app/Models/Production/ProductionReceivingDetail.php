<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionReceivingDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_receiving_detail';

    protected $fillable = [

        'id_penerimaan',
        'id_item',
        'id_satuan',
        'qty_item',
        'created_by',
        'updated_by',
        'deleted_by'

    ];
}
