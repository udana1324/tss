<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionDelivery extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_delivery';
    protected $fillable = [

        'kode_pengiriman',
        'id_supplier',
        'id_alamat',
        'jumlah_total_sj',
        'tanggal_sj',
        'status_pengiriman',
        'flag_revisi',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
