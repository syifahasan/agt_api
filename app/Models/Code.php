<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\AGProductClient;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Code extends Model
{
    protected $table = 'agcodes';

    protected $fillable = ['agCodePackage_id','code','article','status','pin'];

    // protected $with = ['package_code'];

    public $timestamps = false;

    public static function getId($param){
        $id=DB::table('agcodes')
            ->where('agcodes.code','=',$param)
            ->value('agcodes.id');

        return $id;
    }

    public static function getCode($param){
        $code=DB::table('agcodes')
            ->where('agcodes.code','=',$param)
            ->first();

        return $code;
    }

    public function product():HasOne{
        return $this->hasOne(AGProductClient::class, 'agCodes_id', 'id');
    }
}
