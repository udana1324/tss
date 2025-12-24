<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTerms extends Model
{
    use HasFactory;
    protected $table = 'purchase_order_terms';
    protected $fillable = [

        'id_po',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
