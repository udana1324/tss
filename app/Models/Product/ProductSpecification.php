<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecification extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_specification';
    protected $fillable = [
        'kode_spesifikasi',
        'jenis_spesifikasi',
        'nama_spesifikasi',
        'flag_cetak',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
