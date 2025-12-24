<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintLog extends Model
{
    use HasFactory;
    protected $table = 'print_log';
    protected $fillable = [
        'no_dokumen',
        'created_by'
    ];
}
