<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_unit';
    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
        'keterangan_satuan',
        'kode_satuan_pajak',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
