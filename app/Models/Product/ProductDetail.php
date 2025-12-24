<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_detail';
    protected $fillable = [
        'id_product',
        'id_satuan',
        'panjang_item',
        'lebar_item',
        'tinggi_item',
        'berat_item',
        'harga_beli',
        'harga_jual',
        'panjang_dus',
        'lebar_dus',
        'tinggi_dus',
        'berat_dus',
        'qty_per_dus',
        'stok_minimum',
        'stok_maksimum',
        'keterangan_satuan_item',
        'default',
        'flag_monitor',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
