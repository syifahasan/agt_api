<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Appid extends Authenticatable
{
    use Notifiable;

        protected $table = 'zappid';

    protected $fillable = ['clientid','name','id','appid'];
}
