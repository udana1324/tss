<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customer_category';
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
