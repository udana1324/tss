<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceCollection extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchase_invoice_collection';
}
