<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockConversionDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'stock_conversion_detail';
    protected $fillable = [
        'id_conversion',
        'id_item',
        'id_satuan',
        'id_index',
        'qty_item',
        'jenis',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
