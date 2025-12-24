<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderTerms extends Model
{
    use HasFactory;
    protected $table = 'sales_order_terms';
    protected $fillable = [

        'id_so',
        'terms_and_cond',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',

    ];
}
