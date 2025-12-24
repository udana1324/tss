<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product';
    protected $fillable = [
        'kode_item',
        'nama_item',
        'kategori_item',
        'jenis_item',
        'merk_item',
        // 'satuan_item',
        // 'panjang_item',
        // 'lebar_item',
        // 'tinggi_item',
        // 'berat_item',
        // 'panjang_dus',
        // 'lebar_dus',
        // 'tinggi_dus',
        // 'berat_dus',
        // 'qty_per_dus',
        // 'harga_beli',
        // 'harga_jual',
        // 'stok_minimum',
        // 'stok_maksimum',
        'keterangan_item',
        'product_image_path',
        'active',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
