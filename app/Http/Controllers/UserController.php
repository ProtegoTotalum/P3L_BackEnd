<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        //get user
        $user = User::latest()->get();
        //render view with posts
        if(count($user) > 0){
            return new UserResource(true, 'List Data User',
            $user); // return data semua user dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data user kosong

    }

    public function destroy($id)
    {
        $user= User::find($id);

        if(is_null($user)){
            return response([
                'message' => 'User Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($user->delete()){
            return response([
                'message' =>'Delete User Sukses',
                'data' => $user
            ], 200);
        }
        return response([
            'message' => 'Delete User Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $user= User::find($id);

        if(!is_null($user)){
            return response([
                'message' => 'Data User Ditemukan',
                'data' => $user
            ], 404);
        }

        return response([
            'message' => 'Data User Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data user tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required'
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email, 
            'username' => $request->username, 
            'password' => $request->password
        ]);
        
        return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
    }
}
