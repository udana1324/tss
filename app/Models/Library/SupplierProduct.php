<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierProduct extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'supplier_product';
    protected $fillable = [
        'id_supplier',
        'id_item',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
