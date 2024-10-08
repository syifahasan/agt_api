<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class CheckingLog extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['agProductClient','agCodes','agUser','appid','geoLocLangitude','geoLocLongitude'];
    protected $collection = 'agcheckinglog';
    protected $dates = ['created_at','updated_at'];
}
