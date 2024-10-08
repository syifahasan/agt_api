<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Requests\UpdateCodeRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Core\LogActivity as log;
use Illuminate\Support\Facades\Log as logs;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Firebase\VerifyController as verify;
use MongoDB\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Appid;
use App\Models\Mongo\CheckingLog;
use MongoDB\BSON\ObjectId;
use App\Models\Mongo\Claim;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $user;
    public function __construct(User $user){
        $this->user = $user;
    }

    public function check(Request $request)
    {
        if (strlen($request->code) > 12){
            return response()->json(['status' => 'error' , 'msg' => 'Kode yang di scan salah!']);
        }
        if (!isset($request->latitude) || !isset($request->longitude)){
            return response()->json(['status' => 'error', 'message' => 'Lokasi Anda tidak diketahui, Silahkan Aktifkan GPS.'], 422);
        }

        $codes = Code::getId($request->code);

        try{
            if (empty($codes)){
                return response()->json(['status'=>'error', 'result'=>'', 'msg'=>'Code not found']);
            }

            // log::addToLog("Check Code")
            $check = \App\Core\Library::CheckCode($codes);
            $check = (array)$check;
            logs::info('Check result:', [$check]);
                if (!$check) {
                    $pesan = "Potentially Counterfeit Product";
                    $status = "error";
                    $status_ = "";
                    return response()->json(['status' => $status, 'statusClaim' => $status_, 'result' => null, 'message' => $pesan]);
                }
                $pesan = "Code is registered for " . ($check['order']['brand']['Name'] ?? "");
                $status = "success";
                $status_ = "0";
                if (count($check['product']) > 0) {
                    $check['brand'] = $check['order']['brand'];
                    //array_push($check['product'],['brand'=>$check['order']['brand']]);
                    if ($request->appid != "003") {
                        $cekClient = Appid::where('appid', $request->appid)->select('clientid')->first();
                        //info($check);
                        if ((int) $cekClient->clientid != $check['brand']['client']['id']) {
                            return response()->json(['status' => $status, 'statusClaim' => $status_, 'result' => null, 'message' => $pesan]);
                        }
                    }
                    $data = array(
                        'agProductClient_id' => $check['product']['id'],
                        'agCodes_id' => $codes,
                        // 'agUser_id' => $user->id,
                        'appid' => $request->appid,
                        'geoLocLangitude' => $request->longitude,
                        'geoLocLongitude' => $request->latitude,
                    );
                    $jobChecking = new \App\Jobs\Mongo\JobCheckingLog($data);
                    dispatch($jobChecking);
                }
                $check['pathImg'] = asset('product') . '/';
                $check['brand_name'] = isset($check['order']['brand']['Name']) ? $check['order']['brand']['Name'] : "";
                $check['client'] = isset($check['order']['brand']['client']) ? $check['order']['brand']['client'] : "";
                $check['client_name'] = isset($check['client']['name']) ? $check['client']['name'] : "";
                $check['extensn'] = ".jpg";
                unset($check['order']);
                return response()->json(['status' => $status, 'status_' => $status_, 'result' => (object)$check, 'message' => $pesan]);
        }catch (Exception $e) {
            return response()->json(['status' => 'error', 'status' => 'Error', 'message' => 'failed check code']);
        }
    }

    public function mobileCheck(Request $request, $code){
        if (strlen($code) > 12){
            return response()->json(['status' => 'error' , 'msg' => 'Kode yang di scan salah!']);
        }
        if (!isset($request->latitude) || !isset($request->longitude)){
            return response()->json(['status' => 'error', 'message' => 'Lokasi Anda tidak diketahui, Silahkan Aktifkan GPS.'], 422);
        }
        if (!isset($request->appid) || $request->appid === null || trim($request->appid == " ")) {
            return response()->json(['status' => 'Error_appid', 'message' => "Akses ditolak!!"]);
        }
        if (!isset($request->token) || $request->token === null || trim($request->token == " ")) {
            return response()->json(['status' => 'Error_token', 'message' => "Akses ditolak!!"]);
        }

        $pesan = "Code Not Found";
        $status = "error";
        $status_ = "";
        $codes = Code::getId($code);

        try{
            if (empty($codes)) {
                return response()->json(['status' => $status, 'statusClaim' => $status_, 'result' => null, 'message' => $pesan]);
            }
            $user_end = verify::decodeJson($request->token, $request->appid);
            logs::info('decoded user', ['user'=> $user_end->email]);
            if (!is_object($user_end)) {
                return response()->json(['status' => "error", 'message' => "Token Expired"]);
            }
            $user = \App\Core\Library::getUser($user_end->email);
            $userId = $user->id;
            $client = '003';

            // log::addToLog("Check Code", $user->id);

            logs::info('Calling CheckClaim method');
            $check = \App\Core\Library::CheckClaim(null, $codes);

            logs::info('Check Claim result: ', ['check'=> $check]);
            if ($check !== null){
                logs::info('Checking claim status product');
                if(!isset($check['order'])){
                    $check['order'] = array(
                        'brand' => $check['product']['brand']
                    );
                }
                if($check['user']['user_id'] == @$user->id){
                    $pesan = 'Product is Protected by AG, and Product is yours';
                    $status = 'success';
                    $status_ = "Claimed";
                    $agUserId = $user->id;
                }else{
                    $pesan = "Product is Protected by AG, and Product is claimed by other";
                    $status = "success";
                    $status_ = "Claimed";
                    $agUserId = $check['user']['id'];
                    $appid = Appid::where('appid', $request->appid)->first();
                }
                $data = array(
                    'agProductClient_id' => $check['product']['id'],
                    'agCodes_id' => $codes,
                    'agUser_id' => $agUserId,
                    'appid' => $request->appid,
                    'geoLocLatitude' => $request->latitude,
                    'geoLocLongitude' => $request->longitude,
                );
                $jobChecking = new \App\Jobs\Mongo\JobCheckingLog($data);
                dispatch($jobChecking);
            }
            else {
                logs::info('Claim check returned null, falling back to product code check');
                $check = \App\Core\Library::CheckCode($codes);
                logs::info('Checking Product codes');
                if (!$check){
                    $pesan = 'Potentially Counterfeit Product';
                    $status = 'error';
                    $status_ = '';
                    return response()->json(['status'=>$status, 'result' => null, 'message' => $pesan]);
                }
                $pesan = "Code is registered for " . ($check['order']['brand']['Name'] ?? "");
                $status = 'success';
                $status_ = 'Available';
                if(count($check['product']) > 0){
                    $check['brand'] = $check['order']['brand'];
                    $check['user']['id'] = 0;
                    $data = array(
                        'agProductClient_id' => $check['product']['id'],
                        'agCodes_id' => $codes,
                        'agUser_id' => 0,
                        'appid' => $request->appid,
                        'geoLocLatitude' => $request->latitude,
                        'geoLocLongitude' => $request->longitude,
                    );
                    $jobChecking = new \App\Jobs\Mongo\JobCheckingLog($data);
                    dispatch($jobChecking);

                }
            }
            // $check["kta"] = $this->KTA($codes); //find($codes)??'';
            $check['pathImg'] = asset('product') . '/';
            $check['brand_name'] = isset($check['order']['brand']['Name']) ? $check['order']['brand']['Name'] : "";
            $check['client'] = isset($check['order']['brand']['client']) ? $check['order']['brand']['client'] : "";
            $check['client_name'] = isset($check['client']['name']) ? $check['client']['name'] : "";
            $check['extensn'] = ".jpg";
            $check['historyscan'] = $this->HistoryScan($codes);
            $check['historycode'] = $this->HistoryCode($codes); //DB::table('agcheckinglog')->where('agCodes_id',$codes)->count();
	    // $check['agUser_id'] = $user->id;
            // unset($check['order']);
            //info(['status' => $status, 'statusClaim' => $status_, 'result' => (object)$check, 'message' => $pesan]);
            return response()->json(['status' => $status, 'statusClaim' => $status_, 'result' => (object)$check, 'message' => $pesan]);
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'statusClaim' => 'Error', 'message' => 'failed check code']);

        }
        return response()->json(['status' => $status, 'statusClaim' => $status_, 'result' => $check, 'message' => $pesan]);

    }

    protected function HistoryScan($codes)
    {
        return Cache::remember('historyscan:' . $codes, 300, function () use ($codes) {
            return  CheckingLog::orderBy('created_at', 'desc')->select('*')->where('agCodes_id', (int)$codes)->first();
            //CheckingLog::where('agCodes.id',$codes)->first
        });
    }

    protected function HistoryCode($codes)
    {
        return Cache::remember('historyCode:' . $codes, 300, function () use ($codes) {
            return CheckingLog::where('agCodes.id', (int) $codes)->count();
        });
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCodeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Code $code)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Code $code)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCodeRequest $request, Code $code)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Code $code)
    {
        //
    }

    public function tesmongo(){

        try {
            $codeid = 451527088;
            $userid = 31563;
            logs::info('Code ID:', ['codeid' => $codeid]);
            // Test MongoDB connection using DB facade
            // $result = Cache::remember('code:check'. $codeid)

            $data = [
                'claim_id'=>550,
                'user'=> 31563,
                'code'=> 451527088,
                'location'=>['type'=>"Point",
                    'coordinates'=>[12,12]],
                'time'=>'2024-09-27 12:43:53',
                'product'=> '975',
                'approved'=>0,
                'additional'=>['photo'=>''],
                'history'=>[]
            ];

            $result = DB::connection('mongodb')->table('agclaim')->insert($data);

            // $result = DB::connection('mongodb')->table('agclaim')
            //             ->where('claim_id', 226)
            //             // ->where('code.code_id', $codeid)
            //             ->first();



            if ($result) {
                logs::info('Fetched data:', (array)$result);
                return response()->json(['status'=>'success', 'result'=>$result]);
            } else {
                logs::info('No data found in agcodes collection with id:', ['id' => $codeid]);
            }
        } catch (Exception $e) {
            // Log the error message for debugging
            Log::error('MongoDB Connection Error: ' . $e->getMessage());
            return 'Error connecting to MongoDB: ' . $e->getMessage();
        }
    }
}
