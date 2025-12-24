<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBrand extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_brand';
    protected $fillable = [
        'nama_merk',
        'keterangan_merk',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
