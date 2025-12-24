<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionReceiving extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'production_receiving';
    protected $fillable = [

        'kode_penerimaan',
        'id_po',
        'id_alamat',
        'no_sj_supplier',
        'jumlah_total_sj',
        'tanggal_sj',
        'tanggal_terima',
        'status_penerimaan',
        'flag_revisi',
        'flag_invoiced',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
