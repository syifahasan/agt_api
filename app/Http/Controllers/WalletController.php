<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Firebase\VerifyController as verify;
use App\Models\User;
use App\Models\Wallet;
use App\Core\Crypt as cr;

class WalletController extends Controller
{
    public function getWallet(Request $request) {
        try {
            if(!isset($request->appid) || $request->appid === null || trim($request->appid==" ")) {
                    return response()->json(['status' => 'Error_appid', 'message' =>"Access Denied!!"]);
            }
            if(!isset($request->token) || $request->token === null || trim($request->token==" ")) {
                    return response()->json(['status' => 'Error_token', 'message' =>"Access Denied!!"]);
            }

            $user = verify::decodeJson($request->token, $request->appid);
            if(!is_object($user)) {
                return response()->json(['status'=>"error",'message'=>"Token Expired"]);
                }
                $user_end = User::where('email', $user->email)->first();
                $wallet = Wallet::where("aguser_id",$user_end->id)->where('agclient_id',$request->appid)->first();
                if($wallet) {
                return response()->json(['status' => 'success','result'=> cr::decrypt($wallet->id.$wallet->aguser_id,$wallet->nominal),'message'=>'']);
                }
                return response()->json(['status' => 'success','result'=> 0,'message'=>'']);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error','result'=>'','message'=>"Belum Ada koin."]);
        }
        return response()->json(['status' => 'error','result'=>'','message'=>"Belum Ada koin"]);
    }
}
