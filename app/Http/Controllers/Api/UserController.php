<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\Firebase\VerifyController as verify;

class UserController extends Controller
{
    public function verifyemail($token){
        $user = User::where('email_token', $token)->first();

        if (!$user){
            return response()->json(['message' => 'Invalid verification token.'], 404);
        }

        $user->verified = 1;
        $user->email_token = null; // Optional: Clear the token
        $user->save();

        return response()->json(['message' => 'Email verified successfully!'], 200);
    }

    public function login(Request $request) {
        try{
        \Log::info('Using model table: ' . (new User)->getTable());
            //$credentials = $request->only('email', 'password');
        $validator = Validator::make($request->all(), [
                'email'  => 'required|max:255|email',
                'password'               => 'required'
                ]);
            //dd($credentials);
        $errors=$validator->errors();
                    if ($validator->fails()) {
                    return response()->json($errors);
                    }
        $key = $request->appid?$request->appid:'003';

        $aa= Cache::remember('login_'.$key.'_'.$request->email, 60, function () use ($request,$key) {
                    $auth = verify::createUser($key);
                    $sign = $auth->signInWithEmailAndPassword($request->email,$request->password);
                    $token = $sign->idToken();
            \Log::info('Token retrieved', ['token' => $token]);
            return $token;
        });
        return response()->json(["status" => "success", "token" => $aa,"message" => '']);
        }catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
                return response()->json(["status"=>"error","message"=>"Email Dan Password Tidak Cocok"]);
        }catch (\Exception $e) {
                return response()->json(["status"=>"error","message"=>"Tidak Ada Respon"]);
        }

        }


    public function sendVerificationEmail(User $user){
        $token = Str::random(40);

         // Save token to the user's record
        $user->verification_token = $token;
        $user->save();

        // Send verification email
        Mail::send('emails.verify', ['token' => $token], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Email Verification');
        });
    }

    public function sendTestEmail()
    {
        try {
            Mail::send('emails.verify', [], function ($message) {
                $message->to('zakunime@yahoo.com')
                        ->subject('Test Email');
            });

            return response()->json(['message' => 'Email sent successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
        }
    }
}
