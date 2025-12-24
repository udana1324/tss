<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionPeriod extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'transaction_period';
    protected $fillable = [
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
