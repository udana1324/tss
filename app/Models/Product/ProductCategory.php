<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_category';
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'kode_kategori_pajak',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
