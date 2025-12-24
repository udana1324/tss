<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetailSpecification extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_detail_specification';
    protected $fillable = [
        'id_product',
        'id_spesifikasi',
        'value_spesifikasi',
        'flag_cetak',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
