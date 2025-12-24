<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expedition extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'expedition';
    protected $fillable = [
        'nama_ekspedisi',
        'nama_perusahaan',
        'telp_perusahaan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
