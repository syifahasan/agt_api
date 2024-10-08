<?php
namespace App\Core\Observer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mongo\Claim;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use DB;

class ClaimObserver
{
    public function created(Model $model){
        try{
            \Log::info('ClaimObserver created called', ['model' => $model->toArray()]);

            $userData = $this->getUser($model->userId);
            if (isset($userData['id'])){
                $userData['user_id'] = $userData['id'];
                unset($userData['id']);
            }

            $codeData = $this->getCode($model->agCodes_id);
            if (isset($codeData['id'])){
                $codeData['code_id'] = $codeData['id'];
                unset($codeData['id']);
            }

            $data =[
                'claim_id'=>$model->id,
                'user'=> $userData,
                'code'=> $codeData,
                'location'=>['type'=>"Point",
                    'coordinates'=>[$model->geoLocLongClaim,$model->geoLocLangClaim]],
                'time'=>$model->time,
                'product'=> $this->getProduct($model->agProductClient_id),
                'approved'=>$model->approved,
                'additional'=>['photo'=>$model->photo],
                'history'=>[]
            ];

            \Log::info('MongoDB data being inserted', ['data' => $data]);

            $result = DB::connection('mongodb')->table('agclaim')->insert($data);
        }catch (\Exception $e) {
            \Log::error('Error storing claim to MongoDB', ['error' => $e->getMessage()]);
        }

    }

    public function updated(Model $model) {
        //info(['update'=>$model->all()]);
        $code = $this->getCode($model->agCodes_id);
        Claim::where('id',(int)$model->id)->update(['user'=> $this->getUser($model->userId),'code'=> $code,
            'location'=>['type'=>"Point",'coordinates'=>[$model->geoLocLongClaim,$model->geoLocLangClaim]],
            'time'=>$model->time,'product'=> $this->getProduct($model->agProductClient_id),'approved'=>$model->approved,
            'additional'=>['photo'=>$model->photo]]);
        if(Cache::has('code:claim:'.$code['id'])) {
            Cache::forget('code:claim:'.$code['id']);
        }
    }

    protected function getUser($id = null) {
        if(empty($id)) {
            return [];
        }
        $data = Cache::remember('user:'.$id,(5*60), function () use ($id){
            return \App\Models\User::where('id',$id)->first()->toArray();
        });
        return $data;
    }
    protected function getProduct($id = null) {
        if(empty($id)) {
            return [];
        }
        $data = Cache::remember('product:'.$id,(5*60),function () use ($id){
            return \App\Models\Product::where('id',$id)->first()->toArray();
        });
        return $data;
    }
    protected function getCode($id = null) {
        if(empty($id)) {
            return [];
        }
        $data = Cache::remember('code:'.$id,(5*60),function() use ($id){
            return \App\Models\Code::where('id',$id)->first()->toArray();
        });
        return $data;
    }
}

