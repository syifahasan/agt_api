<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'agwallet';

    protected $fillable = ['aguser_id','agclient_id','nominal','debit'];

    protected $hidden = [
        'aguser_id', 'agclient_id',
    ];

}
