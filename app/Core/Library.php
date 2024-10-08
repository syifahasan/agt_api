<?php
namespace App\Core;
use DB;
use App\Models\Mongo\Claim;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class Library{
    public static function CheckCode($idCode) {
        try {
            $data = Cache::remember('code:check:'.$idCode,(10*60),function()use ($idCode){
            $data = DB::connection('mongodb')->table('agcodes')
                        ->where('codeid', (int)$idCode)
                        ->where('order', '!=', [])
                        ->select(['codeid', 'code', 'product', 'order', 'article', 'status', 'pin'])
                        ->first();
            return $data;
            });
            return json_decode(json_encode($data), true);
        } catch (Exception $ex) {
            Log::error('Query error: ' . $ex->getMessage());
            return false;
        }
        return false;
    }

    public static function CheckClaim($userId = null,$codeid = null,$client = null) {
        try {

            // Log::info('CheckClaim method called with userId: ' . ($userId ?? 'null'));

            $isclient = $client ? $client : '003';

            $data = DB::connection('mongodb')->table('agclaim')
                            // ->where('user.user_id', (int)$userId)
                            ->where('code.code_id', (int)$codeid)
                            ->first();

            // Log the result of the query
            Log::info('Query result: ', ['result' => $data]);

            if ($data) {
                // Convert stdClass to array
                return (array) $data;
            }else {
                // Log that no result was found
                Log::info('No claim found for userId: ' . $userId . ' and client: ' . $client);
                return null;
            }

                //});
                //return $data;

        } catch (Exception $ex) {
            Log::error('Error in CheckClaim method: ' . $ex->getMessage());
            return false;
        }
    }

    public static function getUser($email) {
        if(empty($email)) {
            return false;
        }
        $data = Cache::remember('user:email:'.$email,(10*60),function () use ($email){
            return User::where('email',$email)->first();
        });
        if (!$data || !$data instanceof User) {
            return false; // or handle this case as needed
        }

        return $data;
    }
}


?>
