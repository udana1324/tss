<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProduct extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customer_product';
    protected $fillable = [
        'id_customer',
        'id_item',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
