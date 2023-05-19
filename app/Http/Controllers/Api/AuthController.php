<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(Request $request){
        $registrationData = $request->all(); //mengambil seluruh data input dan menyimpannya dalam variable registrationData

        $validate = Validator::make($registrationData, [
            'id_user_login' => 'required',
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required',
            'role' => 'required'
        ]); //rule validasi input saat register

        if($validate->fails())
        return response(['message' => $validate->errors()],400);//error validasi input

        $registrationData['password'] = bcrypt($request->password); //enkripsi password

        $user = User::create($registrationData); //membuat user baru
        $user->sendEmailVerificationNotification();
        event(new Registered($user));

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200); //return data dalam bentuk json
    }

    public function login(Request $request){
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()], 400);

        if(!Auth::attempt($loginData))
        return response(['message' => 'Invalid Credentials'], 401);

        $user = Auth::user();
        if ($user->email_verified_at == NULL) {
            return response([
                'message' => 'Please Verify Your Email'
            ], 401); //Return error jika belum verifikasi email
        }
        $token = $user->createToken('Authentication Token')->accessToken;


        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function logout(Request $request)
    {        
        // $token = $request->user()->token();
        // $token->revoke();
        // $response = ['message' => 'You have been successfully logged out!'];
        // return response($response, 200);
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User Logged Out Successfully',
        ],200);
    }
}
