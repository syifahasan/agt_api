<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Core\Observer\Claim;

class ClaimProduct extends Model
{
    use Claim;
    protected $table = 'aguserproduct';

    protected $fillable = ['agProductClient_id','agCodes_id','userId','time','geoLocLangClaim','geoLocLongClaim','photo','approved'];

    protected $with = ['product','user'];
    public function product(){
        return $this->belongsTo('App\Models\AGProductClient','agProductClient_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','userId');
    }

    public static function joining(){
        $data=ClaimProduct::select(['aguserproduct.*','agproductclient.nama as product_name'
                ,'agclientbrand.Name as brand_name','agcodes.code','aguser.name as agusername'])
            ->join('agproductclient','agproductclient.id','=','aguserproduct.agProductClient_id')
            ->join('agclientbrand','agclientbrand.id','=','agproductclient.agClientBrand_id')
            ->join('agcodes','agcodes.id','=','aguserproduct.agCodes_id')
            ->join('aguser','aguser.id','=','aguserproduct.userId');

        return $data;
    }

    public static function productMostClaim() {
        $data = DB::table('aguserproduct')
        //$data = ClientCheckingLog::
                ->select("agproductclient.nama as nama",DB::raw("count(agclient.id) as jml"))
                ->join('agproductclient','aguserproduct.agProductClient_id','=','agproductclient.id')
                ->join('agclientbrand','agproductclient.agClientBrand_id','=','agclientbrand.id')
                ->join('agclient','agclientbrand.agClient_id','=','agclient.id');
        return $data;

    }
}
