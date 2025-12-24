<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLKasBankTerms extends Model
{
    use HasFactory;
    protected $table = 'gl_kas_bank_terms';
    protected $fillable = [

        'id_kas_bank',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
