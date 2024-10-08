<?php
namespace App\Core\Observer;


use Illuminate\Database\Eloquent\Model;
use App\Model\Mongo\CheckingLog;
class CheckingLogObserver
{
    public function creating(Model $model) {
        CheckingLog::create(['agProductClient'=> \App\Model\ClientProduct_::where('id',$model->agProductClient_id)->first()->toArray(),
            'agCodes'=> \App\Model\Code::where('id',$model->agCodes_id)->first()->toArray(),
            'agUser'=> \App\Model\User::where('id',$model->agUser_id)->first()->toArray(),
            'appid'=> \App\Model\Appid::where('appid',$model->appid)->first()->toArray(),
            'geoLocLangitude'=>$model->geoLocLangitude,'geoLocLongitude'=>$model->geoLocLongitude]);

    }
}
