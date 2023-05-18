<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiDepositReguler;
use App\Models\Promo;
use App\Models\Member;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransaksiDepositRegulerResource;

class TransaksiDepositRegulerController extends Controller
{
    public function index()
    {
        //get transaksi reguler
        $reguler =  TransaksiDepositReguler::with(['pegawai','member', 'promo'])->get();
        //render view with posts
        if(count($reguler) > 0){
            return new TransaksiDepositRegulerResource(true, 'List Data Transaksi Deposit Reguler',
            $reguler); // return data semua transaksi reguler dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data transaksi reguler kosong

    }

    public function store(Request $request){

        if($request->nominal_deposit_reguler < 500000 ){
            return response(
                ['message'=> 'Transaksi Gagal, Minimal Deposit Reguler Sebesar Rp 500.000',] , 400);
        }

        try
        {
            $id_promo = null;
            if($request->id_promo != null){
                $promo = Promo::findorfail($request->id_promo);
                $minimal_deposit = $promo->minimal_deposit;
                $nominal_deposit_reguler = $request->nominal_deposit_reguler;
                $total_deposit_reguler = $request->nominal_deposit_reguler;
                if($nominal_deposit_reguler >= $minimal_deposit){
                    $id_promo = $request->id_promo;
                    $bonus_deposit_reguler = $promo->bonus_deposit;
                    $total_deposit_reguler = $total_deposit_reguler + $bonus_deposit_reguler;
                }else{
                    $id_promo = null;
                    $bonus_deposit_reguler = 0;
                    $total_deposit_reguler = $nominal_deposit_reguler;
                }
                
            }else{
                $nominal_deposit_reguler = $request->nominal_deposit_reguler;
                $bonus_deposit_reguler = 0;
                $total_deposit_reguler = $nominal_deposit_reguler;
            }

            $reguler = TransaksiDepositReguler::firstOrCreate  ([
                'id_pegawai' => $request->id_pegawai,
                'id_member'=> $request->id_member,
                'id_promo' => $id_promo,
                'tanggal_deposit_reguler' => date('Y-m-d H:i:s', strtotime('now')),
                'nominal_deposit_reguler' => $nominal_deposit_reguler,
                'bonus_deposit_reguler' => $bonus_deposit_reguler,
                'total_deposit_reguler' =>   $total_deposit_reguler,

            ]);
            //Update data di tabel member
            //cari data member
            $member = Member::find($request->id_member);
            $pegawai = Pegawai::find($request->id_pegawai);
            $before = $member->sisa_deposit_reguler;
            $member->sisa_deposit_reguler =  $before + $total_deposit_reguler;
            $member->save();
            return response([
                'message'=> 'Transaksi Deposit Reguler Berhasil',
                'data' => ['transaksi_deposit_reguler' => $reguler, 'sisa_deposit' => $before, 'nomor_struk_deposit_reguler' => TransaksiDepositReguler::latest()->first()->nomor_struk_deposit_reguler, 'nama_member' => $member->nama_member, 'nomor_member' => $member->nomor_member, 'nama_pegawai' => $pegawai->nama_pegawai],
                'total' => $total_deposit_reguler,
            ]);

        } catch(Exception $e){
            dd($e);
        }
    }

    public function destroy($id)
    {
        $reguler= TransaksiDepositReguler::find($id);

        if(is_null($reguler)){
            return response([
                'message' => 'Transaksi Reguler Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($reguler->delete()){
            return response([
                'message' =>'Delete Transaksi Reguler Sukses',
                'data' => $reguler
            ], 200);
        }
        return response([
            'message' => 'Delete Jadwal Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $reguler= TransaksiDepositReguler::find($id);

        if(!is_null($reguler)){
            return response([
                'message' => 'Transaksi Reguler Ditemukan',
                'data' => $reguler
            ], 404);
        }

        return response([
            'message' => 'Transksi Reguler Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data jadwal tidak ditemukan
    }

}
