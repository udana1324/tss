<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransferDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'stock_transfer_detail';
    protected $fillable = [
        'id_transfer',
        'id_item',
        'qty_item',
        'id_index_f',
        'id_index_t',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
