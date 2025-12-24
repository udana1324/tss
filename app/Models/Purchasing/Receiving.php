<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receiving extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'receiving';
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
