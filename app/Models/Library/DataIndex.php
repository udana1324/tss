<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataIndex extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'data_index';
    protected $fillable = [
        'kode_index',
        'nama_index',
        'parent',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
