<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroupDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customer_group_detail';
    protected $fillable = [
        'id_group',
        'id_customer',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
