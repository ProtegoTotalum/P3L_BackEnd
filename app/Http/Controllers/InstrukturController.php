<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instruktur;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\InstrukturResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;


class InstrukturController extends Controller
{
    public function index()
    {
        //get instruktur
        //$instruktur = Instruktur::latest()->get();
        $instruktur =  Instruktur::with(['user'])->get();
        //render view with posts
        if(count($instruktur) > 0){
            return new InstrukturResource(true, 'List Data Instruktur',
            $instruktur); // return data semua instruktur dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data instruktur kosong

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'nama_instruktur' => 'required',
            'email_instruktur' => 'required|email:rfc,dns',
            'nomor_telepon_instruktur' => 'required',
            'username_instruktur' => 'required|unique:users,username',
            'password_instruktur' => 'required',
            'jumlah_keterlambatan_instruktur' => 'required'
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $instruktur = Instruktur::create([ 
            'nama_instruktur' => $request->nama_instruktur, 
            'email_instruktur' => $request->email_instruktur, 
            'nomor_telepon_instruktur' => $request->nomor_telepon_instruktur,
            'username_instruktur' => $request->username_instruktur,
            'password_instruktur' => $request->password_instruktur,
            'jumlah_keterlambatan_instruktur' => $request->jumlah_keterlambatan_instruktur
        ]);
        //Create data new user
        $user = new User();
        $user->id_user_login = $instruktur->id;
        $user->name = $instruktur->nama_instruktur;
        $user->email = $instruktur->email_instruktur;
        $user->username = $instruktur->username_instruktur;
        $user['password'] = bcrypt($instruktur->password_instruktur); //enkripsi password
        $user->role = "instruktur";
        $user->save();
        $iduser = $user->id;
                
        $instruktur->id_user = $iduser;
        $instruktur->save();
        
        $user->sendEmailVerificationNotification();
        event(new Registered($user));
        
        return response([
            'success' => 'true',
            'message'=> 'Berhasil Menambahkan Instruktur',
           'data' => ['instruktur' => $instruktur, 'user' => $user],
        ]);
   
    }

    public function destroy($id)
    {
        $instruktur= Instruktur::find($id);

        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($instruktur->delete()){
            $user= User::find($instruktur->id_user);
            $user->delete();
            return response([
                'message' =>'Delete Instruktur Sukses',
                'data' => $instruktur
            ], 200);
        }
        return response([
            'message' => 'Delete Instruktur Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $instruktur= Instruktur::find($id);

        if(!is_null($instruktur)){
            $user= User::find($instruktur->id_user);
            return response([
                'message' => 'Data Instruktur Ditemukan',
                'data' => $instruktur,
                'dataUser' => $user
            ], 404);
        }

        return response([
            'message' => 'Data Instruktur Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data instruktur tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'nama_instruktur' => 'required',
            'email_instruktur' => 'required',
            'nomor_telepon_instruktur' => 'required',
            'username_instruktur' => 'required',
            'password_instruktur' => 'required',
            'jumlah_keterlambatan_instruktur' => 'required'
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $instruktur = Instruktur::find($id);
        $instruktur->update([
            'id_user' => $request->id_user,
            'nama_instruktur' => $request->nama_instruktur, 
            'email_instruktur' => $request->email_instruktur, 
            'nomor_telepon_instruktur' => $request->nomor_telepon_instruktur,
            'username_instruktur' => $request->username_instruktur,
            'password_instruktur' => $request->password_instruktur,
            'jumlah_keterlambatan_instruktur' => $request->jumlah_keterlambatan_instruktur
        ]);

        $user = User::find($request->id_user);

        $user->name = $instruktur->nama_instruktur;
        $user->username = $instruktur->username_instruktur;
        $user->password = $instruktur->password_instruktur;
        $user->save();
        // alihkan halaman ke halaman departemen
        return response([
            'success' => 'true',
            'message'=> 'Berhasil Update Instruktur',
            'data' => ['instruktur' => $instruktur, 'user' => $user],
        ]);
    }
}
