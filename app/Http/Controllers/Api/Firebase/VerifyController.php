<?php

namespace App\Http\Controllers\Api\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Auth;

class VerifyController extends Controller
{
    public static function decodeJson($token, $appid){


        if (!isset($token) || $token === null || trim($token) == "" || $token == "null") {
            Log::error('Token is missing or invalid');
            return (object)['status' => 'Error', 'message' => "Access Denied!!"];
        }
        if (!isset($appid) || $appid === null || trim($appid) == "" || $appid == "null") {
            Log::error('App ID is missing or invalid');
            return (object)['status' => 'Error', 'message' => "Access Denied!!"];
        }


        try{
            $name = 'AG';
            $raw_pkeys = Cache::remember($name, 10*60, function () {
                return file_get_contents("https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com");
            });
            $pkeys = json_decode($raw_pkeys, true);

            $header = explode('.', $token)[0];
            $decoded_header = json_decode(base64_decode($header));

            if (!isset($pkeys[$decoded_header->kid])) {
                Log::error('Invalid kid:', ['kid' => $decoded_header->kid]);
                return response()->json(['status' => 'Error', 'message' => 'Invalid token kid.']);
            }

            JWT::$leeway += 1.5;
            $decoded = JWT::decode($token, new Key($pkeys[$decoded_header->kid], 'RS256'));
            if (!isset($decoded->email)) {
                Log::error('Decoded token is missing email', (array)$decoded);
                return (object)['status' => 'Error', 'message' => 'Email not found in token'];
            }

            Log::info('Token decoded successfully', (array)$decoded);
            return (object)$decoded;

        }catch(\Firebase\JWT\ExpiredException $ex){
            Log::error("Token Expired", ['exception' => $ex]);
            return (object)['status' => 'Error', 'message' => "Token Expired!"];
        }catch(\Firebase\JWT\SignatureInvalidException $ex){
            Log::error('Invalid token signature', ['exception' => $ex]);
            return (object)['status' => 'Error', 'message' => "Invalid Token Signature!"];
        }catch(\Exception $ex){
            Log::error('Invalid token', ['exception' => $ex]);
            return (object)['status' => 'Error', 'message' => 'Invalid Token!'];
        }
    }

    public static function createUser($appid){
        try{
            $serviceAccount = storage_path('app/AG.json');
            $firebase = (new Factory)->withServiceAccount($serviceAccount);
            $auth = $firebase->createAuth();
            return $auth;
        } catch (Exception $ex) {
            info(['auth_firebase'=>$ex->getMessage()]);

        }
    }
}
