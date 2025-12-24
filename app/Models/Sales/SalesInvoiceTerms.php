<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceTerms extends Model
{
    use HasFactory;
    protected $table = 'sales_invoice_terms';
    protected $fillable = [

        'id_invoice',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
