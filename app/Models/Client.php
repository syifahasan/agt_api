<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'agclient';

    protected $fillable = ['name','address','phone','personInCharge','email','web','clientType_id','memberUntil','admin_users_id'];

    // protected $with = ['type'];
    protected $hidden = [
        'admin_users_id',
    ];

}
