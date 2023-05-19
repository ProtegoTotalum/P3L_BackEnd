<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PegawaiResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class PegawaiController extends Controller
{
    public function index()
    {
        //get pegawai
        $pegawai = Pegawai::with(['user'])->get();
        //render view with posts
        if(count($pegawai) > 0){
            return new PegawaiResource(true, 'List Data Pegawai',
            $pegawai); // return data semua pegawai dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data pegawai kosong

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_pegawai' => 'required',
            'email_pegawai' => 'required|email:rfc,dns',
            'nama_jabatan_pegawai' => 'required',
            'nomor_telepon_pegawai' => 'required',
            'username_pegawai' => 'required|unique:users,username',
            'password_pegawai' => 'required',
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $pegawai = Pegawai::create([ 
            'nama_pegawai' => $request->nama_pegawai,
            'email_pegawai' => $request->email_pegawai,
            'nama_jabatan_pegawai' => $request->nama_jabatan_pegawai, 
            'nomor_telepon_pegawai' => $request->nomor_telepon_pegawai,
            'username_pegawai' => $request->username_pegawai,
            'password_pegawai' => $request->password_pegawai,
        ]);

        //return new PegawaiResource(true, 'Data Pegawai Berhasil Ditambahkan!', $pegawai);
        //Create data new user
        $user = new User();
        $user->id_user_login = $pegawai->id;
        $user->name = $pegawai->nama_pegawai;
        $user->email = $pegawai->email_pegawai;
        $user->username = $pegawai->username_pegawai;
        $user->password = $pegawai->password_pegawai;
        $user->role = $pegawai->nama_jabatan_pegawai;
        $user['password'] = bcrypt($pegawai->password_pegawai); //enkripsi password
        $user->save();
        $iduser = $user->id;
                
        $pegawai->id_user = $iduser;
        $pegawai->save();
        
        $user->sendEmailVerificationNotification();
        event(new Registered($user));
        
        return response([
            'success' => 'true',
            'message'=> 'Berhasil Menambahkan Pegawai',
            'data' => ['pegawai' => $pegawai, 'user' => $user],
        ]);
    }

    public function destroy($id)
    {
        $pegawai= Pegawai::find($id);

        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($pegawai->delete()){
            $user= User::find($pegawai->id_user);
            $user->delete();
            return response([
                'message' =>'Delete Pegawai Sukses',
                'data' => $pegawai
            ], 200);
        }
        return response([
            'message' => 'Delete Pegawai Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $pegawai= Pegawai::find($id);

        if(!is_null($pegawai)){
            return response([
                'message' => 'Data Pegawai Ditemukan',
                'data' => $pegawai
            ], 404);
        }

        return response([
            'message' => 'Data Pegawai Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data pegawai tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_pegawai' => 'required',
            'email_pegawai' => 'required|email:rfc,dns',
            'nama_jabatan_pegawai' => 'required',
            'nomor_telepon_pegawai' => 'required',
            'username_pegawai' => 'required|unique:users,username',
            'password_pegawai' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pegawai = Pegawai::find($id);
        $pegawai->update([
            'nama_pegawai' => $request->nama_pegawai,
            'email_pegawai' => $request->email_pegawai,
            'nama_jabatan_pegawai' => $request->nama_jabatan_pegawai, 
            'nomor_telepon_pegawai' => $request->nomor_telepon_pegawai,
            'username_pegawai' => $request->username_pegawai,
            'password_pegawai' => $request->password_pegawai,
        ]);
        // alihkan halaman ke halaman departemen
        //return new PegawaiResource(true, 'Data Pegawai Berhasil Diupdate!', $pegawai);
        $user = User::find($request->id_user);

        $user->name = $pegawai->nama_pegawai;
        $user->username = $pegawai->username_pegawai;
        $user->password = $pegawai->password_pegawai;
        $user->save();

        return response([
            'success' => 'true',
            'message'=> 'Berhasil Update Pegawai',
            'data' => ['pegawai' => $pegawai, 'user' => $user],
        ]);
    }
}
