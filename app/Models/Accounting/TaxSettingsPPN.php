<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxSettingsPPN extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'tax_settings_ppn';
}
