<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use DB;

class Codes extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['id','codepackage','code','article','status','pin','product','additional','order'];
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $primaryKey = '_id';
    protected $collection = 'agcodes';

    public static function unsignCode($product) {
        $brand = DB::table('agproductclient')->where('id',$product)->value('agClientBrand_id');
        $data = DB::connection('mongodb')->table('agcodes')
                ->select(['code','product'])
                ->where('product',[])
                ->where('order.brand.id',(int)$brand);
        return $data;
    }
}
