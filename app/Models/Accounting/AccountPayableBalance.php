<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayableBalance extends Model
{
    use HasFactory;
    protected $table = 'account_payable_balance';
    protected $fillable = [
        'id_invoice',
        'id_supplier',
        'nominal_invoice',
        'nominal_outstanding',
        'tanggal_invoice',
        'tanggal_jt',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
