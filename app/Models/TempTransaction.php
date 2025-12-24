<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'temp_transaction';
    protected $fillable = [
        'module',
        'id_detail',
        'value1',
        'value2',
        'value3',
        'value4',
        'value5',
        'value6',
        'value7',
        'value8',
        'value9',
        'value10',
    ];
}
