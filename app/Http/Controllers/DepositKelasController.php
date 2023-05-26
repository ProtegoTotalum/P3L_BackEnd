<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepositKelas;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DepositKelasResource;
use Illuminate\Support\Facades\DB;

class DepositKelasController extends Controller
{
    public function index()
    {
        //get deposit kelas
        $depositkelas =  DepositKelas::with(['member','kelas'])->get();
        //render view with posts
        if(count($depositkelas) > 0){
            return new DepositKelasResource(true, 'List Data Deposit Kelas',
            $depositkelas); // return data semua deposit kelas dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data deposit kelas kosong

    }
    
    public function store(Request $request){
        $depositkelas = DepositKelas::create([ 
            'id_member' => $request->id_member,
            'id_kelas' => $request->id_kelas, 
            'sisa_deposit_kelas' => $request->sisa_deposit_kelas,
            'masa_berlaku_deposit_kelas' => $request->masa_berlaku_deposit_kelas,
        ]);

        return new DepositKelasResource(true, 'Data Deposit Kelas Berhasil Ditambahkan!', $depositkelas);
    }

    public function destroy($id)
    {
        $depositkelas= DepositKelas::find($id);

        if(is_null($depositkelas)){
            return response([
                'message' => 'Deposit Kelas Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($depositkelas->delete()){
            return response([
                'message' =>'Delete Deposit Sukses',
                'data' => $depositkelas
            ], 200);
        }
        return response([
            'message' => 'Delete Deposit Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $depositkelas = DB::table('deposit_kelas')
        ->join('kelas', 'deposit_kelas.id_kelas', '=', 'kelas.id')
        ->join('members', 'deposit_kelas.id_member', '=', 'members.id')
        ->select(
            'deposit_kelas.id as id_deposit_kelas',
            'deposit_kelas.id_member as id_member',
            'deposit_kelas.id_kelas as id_kelas',
            'members.nama_member as nama_member',
            'kelas.nama_kelas as nama_kelas',
            'deposit_kelas.sisa_deposit_kelas as sisa_deposit_kelas',
            'deposit_kelas.masa_berlaku_deposit_kelas as masa_berlaku_deposit_kelas',
        )
        ->where('deposit_kelas.id_member', '=', $id)
        ->get();

        if(!is_null($depositkelas)){
            return response([
                'success' => true,
                'message' => 'Data Deposit Kelas Ditemukan',
                'data' => $depositkelas
            ], 200);
        }

        return response([
            'message' => 'Data Deposit Kelas Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data presensi instruktur tidak ditemukan
    }
}
