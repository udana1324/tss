<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'company_account';
    protected $fillable = [
        'nomor_rekening',
        'bank',
        'cabang',
        'atas_nama',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
