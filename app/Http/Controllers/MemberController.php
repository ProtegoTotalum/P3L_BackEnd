<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class MemberController extends Controller
{
    public function index()
    {
        //get member
        //$member = Member::latest()->get();
        $member =  Member::with(['user'])->get();
        //render view with posts
        if(count($member) > 0){
            return new MemberResource(true, 'List Data Member',
            $member); // return data semua member dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data member kosong

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_member' => 'required',
            'email_member' => 'required',
            'nomor_telepon_member' => 'required',
            'tanggal_lahir_member' => 'required',
            'alamat_member' => 'required',
            'sisa_deposit_reguler' => 'required',
            'status_member' => 'required',
            'username_member' => 'required',
            'password_member' => 'required',
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $member = Member::create([ 
            'nama_member' => $request->nama_member,
            'email_member' => $request->email_member, 
            'nomor_telepon_member' => $request->nomor_telepon_member,
            'tanggal_lahir_member' => $request->tanggal_lahir_member,
            'alamat_member' => $request->alamat_member,
            'sisa_deposit_reguler' => $request->sisa_deposit_reguler,
            'masa_berlaku_member' => $request->masa_berlaku_member,
            'status_member' => $request->status_member,
            'username_member' => $request->username_member,
            'password_member' => $request->password_member,
        ]);

        //Create data new user
        $user = new User();
        $user->name = $member->nama_member;
        $user->email = $member->email_member;
        $user->username = $member->username_member;
        $user->password = $member->password_member;
        $user->save();
        $iduser = $user->id;
        
        $member->id_user = $iduser;
        $member->save();

        $user->sendEmailVerificationNotification();
        event(new Registered($user));

        return response([
            'success' => 'true',
            'message'=> 'Berhasil Menambahkan Member',
            'data' => ['member' => $member, 'user' => $user],
        ]);
    }

    public function destroy($id)
    {
        $member= Member::find($id);

        if(is_null($member)){
            return response([
                'message' => 'Member Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($member->delete()){
            $user= User::find($member->id_user);
            $user->delete();
            return response([
                'message' =>'Delete Member Sukses',
                'data' => $member
            ], 200);
        }
        return response([
            'message' => 'Delete Member Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $member= Member::find($id);

        if(!is_null($member)){
            $user= User::find($member->id_user);
            return response([
                'message' => 'Data Member Ditemukan',
                'data' => $member,
                'dataUser' => $user
            ], 404);
        }

        return response([
            'message' => 'Data Member Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data member tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'nama_member' => 'required',
            'email_member' => 'required',
            'nomor_telepon_member' => 'required',
            'tanggal_lahir_member' => 'required',
            'alamat_member' => 'required',
            'sisa_deposit_reguler' => 'required',
            'status_member' => 'required',
            'username_member' => 'required',
            'password_member' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $member = Member::find($id);
        $member->update([
            'id_user' => $request->id_user,
            'nama_member' => $request->nama_member,
            'email_member' => $request->email_member, 
            'nomor_telepon_member' => $request->nomor_telepon_member,
            'tanggal_lahir_member' => $request->tanggal_lahir_member,
            'alamat_member' => $request->alamat_member,
            'sisa_deposit_reguler' => $request->sisa_deposit_reguler,
            'masa_berlaku_member' => $request->masa_berlaku_member,
            'status_member' => $request->status_member,
            'username_member' => $request->username_member,
            'password_member' => $request->password_member,
        ]);
        $user = User::find($request->id_user);

        $user->name = $member->nama_member;
        $user->username = $member->username_member;
        $user->password = $member->password_member;
        $user->save();
        // $user->update([
        //     'name' => $member->nama_member,
        //     'email' => $member->email_member, 
        //     'username' => $member->username_member,
        //     'password' => $member->password_member,
        // ]);

        return response([
            'success' => 'true',
            'message'=> 'Berhasil Update Member',
            'data' => ['member' => $member, 'user' => $user],
        ]);
    }

    public function resetPassword($id)
    {
        $tanggal = DB::table('members')->where('id', $id)->value('tanggal_lahir_member');
        $member = Member::find($id);
        $member->update([
            'password_member' => $tanggal,
        ]);
        $user = User::find($member->id_user);
        $user->password = $tanggal;
        $user->save();
        // alihkan halaman ke halaman departemen
        return new MemberResource(true, 'Password Member Berhasil Direset!', $member);
    }
}
