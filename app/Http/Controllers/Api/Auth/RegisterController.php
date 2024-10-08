<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Illuminate\Support\Str;
// use Kreait\Firebase\ServiceAccount;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        // dd(config('services.firebase.credentials'));

            if (User::where("email", $request->email)->first()) {
                return response()->json(['status' => 'error', 'message' => 'Email already registered'], 409);
	        }
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'username' => 'required|string',
                'password' => 'required|string',
                'name' => 'required|string',
                'dateOfBirth' => 'required|date_format:Y-m-d',
                'gender' => 'required|string',
                'phone' => 'required|string',
                'address' => 'required|string',
            ]);

            $filenamephoto = "";
            $filenameidcard = "";

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
            }

            $gender = $request->gender == 1 ? 'Male' : 'Female';

            // $firebaseCredentialsPath = config('services.firebase.credentials');
            // \Log::info("Firebase Credentials Path: " . $firebaseCredentialsPath);

            try{

                $firebaseUser = $this->createFirebaseAuthUser($request);

                $this->storeInFirebaseDatabase($firebaseUser, $request, $gender);

                $user = $this->storeInMySqlDatabase($request);

            return response()->json(['message' => 'User created successfully!', 'user' => $user], 201);

        }catch(\Kreait\Firebase\Exception\AuthException $e){

            \Log::error('User registration failed: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }catch (\Kreait\Firebase\Exception\DatabaseException $e) {
            \Log::error('Firebase database error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }

    private function createFirebaseAuthUser($request){
        $serviceAccount = storage_path('app/AG.json');
        $firebaseAuth = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->createAuth();

        // Create the user in Firebase Authentication
        $firebaseUser = $firebaseAuth->createUser([
            'email' => $request->email,
            'password' => $request->password,
            'displayName' => $request->name,
        ]);

        return $firebaseUser;
    }

    private function storeInFirebaseDatabase($firebaseUser, $request, $gender){
        $serviceAccount = storage_path('app/AG.json');
        $firebaseDb = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://authenticguards-8dee8-default-rtdb.firebaseio.com/')
            ->createDatabase();

        // Write to Firebase Database
        $firebaseDb->getReference('user/' . $firebaseUser->uid)
            ->set([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'dateOfBirth' => $request->dateOfBirth,
                'gender' => $gender,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
    }

    private function storeInMySqlDatabase($request){
        // Store the user in MySQL Database

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'dateOfBirth' => Carbon::createFromFormat('Y-m-d', $request->dateOfBirth),
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'email_token' => $token,
        ]);
    }

}
