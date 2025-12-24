<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceCollectionTerms extends Model
{
    use HasFactory;
    protected $table = 'purchase_invoice_collection_terms';
    protected $fillable = [

        'id_tf',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
