<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use DB;
use App\Core\Observer\Claim as claimobs;

class Claim extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['claim_id','user','code','location','time','product','approved','additional','history'];
    protected $dates = ['created_at','updated_at'];
    protected $primaryKey = '_id';
    protected $collection = 'agclaim';

    // public function kta() {
    //     return $this->hasOne('App\Model\Kta', 'agCodes_id', 'code.id');
    // }
}
