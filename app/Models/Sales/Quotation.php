<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'quotation';
    protected $fillable = [

        'no_quotation',
        'id_customer',
        'id_alamat',
        'jumlah_total_quotation',
        'nominal_quotation',
        'ppn_quotation',
        'grand_total_quotation',
        'tanggal_quotation',
        'status_quotation',
        'flag_ppn',
        'flag_revisi',
        'jumlah_revisi',
        'id_ppn',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
