<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDeliveryTerms extends Model
{
    use HasFactory;
    protected $table = 'delivery_terms';
    protected $fillable = [

        'id_delivery',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
