<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpeditionBranch extends Model
{
    use HasFactory;
    protected $table = 'expedition_branch';
    protected $fillable = [
        'id_expedisi',
        'nama_cabang',
        'alamat_cabang',
        'kota_cabang',
        'telp_cabang',
        'default',
        'created_by',
        'updated_by'
    ];
}
