<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLJournalDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'gl_journal_detail';
    protected $fillable = [
        'id_journal',
        'id_account',
        'id_sumber',
        'deskripsi',
        'sumber',
        'side',
        'nominal',
        'tanggal_transaksi',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
