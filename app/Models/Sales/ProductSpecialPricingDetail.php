<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecialPricingDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_special_pricing_detail';
}
