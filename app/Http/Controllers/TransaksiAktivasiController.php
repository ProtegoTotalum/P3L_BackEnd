<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiAktivasi;
use App\Models\Pegawai;
use App\Models\Member;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransaksiAktivasiResource;
use Carbon\Carbon;

class TransaksiAktivasiController extends Controller
{
    public function index()
    {
        //get transaksi aktivasi
        $aktivasi =  TransaksiAktivasi::with(['pegawai','member'])->get();
        //render view with posts
        if(count($aktivasi) > 0){
            return new TransaksiAktivasiResource(true, 'List Data Transaksi Aktivasi',
            $aktivasi); // return data semua transaksi aktivasi dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data transaksi aktivasi kosong
    }

    public function store(Request $request){

        if($request->nominal_transaksi_aktivasi != 3000000 ){
            return response(
                ['message'=> 'Transaksi Gagal, Transaksi Aktivasi Sebesar Rp 3.000.000',] , 400);
        }else{
            $aktivasi = TransaksiAktivasi::firstOrCreate  ([
                'id_pegawai' => $request->id_pegawai,
                'id_member'=> $request->id_member,
                'tanggal_transaksi_aktivasi' => date('Y-m-d H:i', strtotime('now')),
                'nominal_transaksi_aktivasi' => $request->nominal_transaksi_aktivasi,
            ]);
            //Update data di tabel member
            //cari data member
            $member = Member::find($request->id_member);
            $pegawai = Pegawai::find($request->id_pegawai);
            if($member->masa_berlaku_member == null){
                //$tgl_aktivasi = $aktivasi->tanggal_transaksi_aktivasi;
                //$masa_berlaku = date('Y-m-d', strtotime('+1 year', strtotime($tgl_aktivasi)));
                $masa_berlaku = Carbon::parse($aktivasi->tanggal_transaksi_aktivasi)->addYear()->format('Y-m-d');
                $member->masa_berlaku_member =  $masa_berlaku;
                $member->status_member =  "Aktif";
                $member->save();
            }else{
                $masa_berlaku = $member->masa_berlaku_member; 
                $member->masa_berlaku_member = $masa_berlaku;
                $member->status_member =  "Aktif";
                $member->save();
            }
            
            return response([
                'message'=> 'Transaksi Aktivasi Berhasil',
                'data' => ['transaksi_aktivasi' => $aktivasi, 'nomor_struk_transaksi_aktivasi' => TransaksiAktivasi::latest()->first()->nomor_struk_transaksi_aktivasi, 'nama_member' => $member->nama_member, 'nomor_member' => $member->nomor_member, 'nama_pegawai' => $pegawai->nama_pegawai, 'masa_berlaku_member' => $member->masa_berlaku_member,],
            ]);
        }
    }
}
