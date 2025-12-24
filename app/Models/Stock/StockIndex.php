<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class StockIndex extends Model
{
    use HasFactory;
    use SoftDeletes;
    use NodeTrait;
    protected $table = 'stock_index';
    protected $fillable = [
        'jenis_index',
        'nama_index',
        '_lft',
        '_rgt',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
