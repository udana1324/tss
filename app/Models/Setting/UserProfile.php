<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';

    protected $fillable = [

        'user_name',
        'user_group',
        'nama_user',
        'telp_user',
        'email_user',
        'active',
        'created_by',
        'updated_by'

    ];
}
