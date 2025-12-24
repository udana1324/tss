<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpeditionTarif extends Model
{
    use HasFactory;
    protected $table = 'expedition_tarif';
    protected $fillable = [
        'id_expedisi',
        'nama_kota',
        'tarif',
        'created_by',
        'updated_by'
    ];
}
