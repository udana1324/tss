<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customer_group';
    protected $fillable = [
        'kode_group',
        'nama_group',
        'flag_harga',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
