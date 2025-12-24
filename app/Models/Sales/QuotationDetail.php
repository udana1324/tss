<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'quotation_detail';
    protected $fillable = [

        'id_quotation',
        'id_item',
        'id_satuan',
        'harga_jual',
        'qty_item',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
