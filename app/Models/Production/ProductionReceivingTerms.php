<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionReceivingTerms extends Model
{
    use HasFactory;
    protected $table = 'receiving_terms';
    protected $fillable = [

        'id_receiving',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
