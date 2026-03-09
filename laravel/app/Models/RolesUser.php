<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolesUser extends Model
{
    protected $table = 'role_user';

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    public $incrementing = false;
    public $timestamps = false;


}
