<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AGProductClient extends Model
{
    protected $table = 'agproductclient_agcodes';
    protected $fillable = ['agProductClient_id', 'agCodes_id'];
    protected $with = ['product','claim'];

    public function product() {
        return $this->belongsTo('App\Models\Product', 'agProductClient_id','id');
    }

    public function code() {
        return $this->belongsTo('App\Models\Code', 'agCodes_id');
    }

    public function claim() {
        return $this->belongsTo('App\Models\ClaimItem', 'agCodes_id', 'agCodes_id')->where('approved','1');
    }
}
