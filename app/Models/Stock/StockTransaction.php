<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'stock_transaction';
    protected $fillable = [
        'kode_transaksi',
        'id_item',
        'id_satuan',
        'qty_item',
        'tgl_transaksi',
        'jenis_transaksi',
        'transaksi',
        'id_index',
        'jenis_sumber',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
