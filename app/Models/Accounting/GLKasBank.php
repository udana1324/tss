<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLKasBank extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_kas_bank';
    protected $fillable = [
        'nomor_kas_bank',
        'id_ar_ap',
        'id_account',
        'id_account_sub',
        'nominal_transaksi',
        'jenis_transaaksi',
        'jenis',
        'tanggal_transaksi',
        'status',
        'flag_entry',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
