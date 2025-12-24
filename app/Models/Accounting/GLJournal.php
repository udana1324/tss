<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLJournal extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_journal';
    protected $fillable = [
        'kode_ref',
        'id_account',
        'id_sumber',
        'deskripsi',
        'sumber',
        'side',
        'nominal',
        'tanggal_transaksi',
        'status',
        'jenis',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
