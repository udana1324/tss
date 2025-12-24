<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsAndConditionTemplateDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'terms_and_condition_template_detail';
    protected $fillable = [
        'id_template',
        'terms_and_condition',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
