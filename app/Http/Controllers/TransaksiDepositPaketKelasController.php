<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiDepositPaketKelas;
use App\Models\Promo;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\DepositKelas;
use App\Models\Kelas;
use Exception;
use App\Http\Resources\TransaksiDepositPaketKelasResource;
use Carbon\Carbon;

class TransaksiDepositPaketKelasController extends Controller
{
    public function index()
    {
        //get transaksi paket kelas
        $paketKelas =  TransaksiDepositPaketKelas::with(['pegawai','member', 'promo', 'kelas'])->get();
        //render view with posts
        if(count($paketKelas) > 0){
            return new TransaksiDepositPaketKelasResource(true, 'List Data Transaksi Deposit Paket Kelas',
            $paketKelas); // return data semua transaksi deposit paket kelas dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data transaksi deposit paket kelas kosong
    }

    public function store(Request $request){
        try
        {
            $id_promo = null;
            if($request->id_promo != null){
                $promo = Promo::findorfail($request->id_promo);
                $kelas = Kelas::findorfail($request->id_kelas);
                $member = Member::findorfail($request->id_member);
                $minimal_deposit = $promo->minimal_deposit;
                $nominal_deposit_paket_kelas = $request->nominal_deposit_paket_kelas;
                $total_deposit_paket_kelas = $request->nominal_deposit_paket_kelas;
                if($nominal_deposit_paket_kelas >= $minimal_deposit){
                    if($minimal_deposit == 5){
                        $id_promo = $request->id_promo;
                        $id_kelas = $request->id_kelas;
                        $id_member = $request->id_member;
                        $bonus_deposit_paket_kelas = $promo->bonus_deposit;
                        $masa_berlaku_deposit_kelas = Carbon::now()->addMonth();
                        $nominal_uang_deposit_paket_kelas = $kelas->harga_kelas * $nominal_deposit_paket_kelas;
                        $total_deposit_paket_kelas = $total_deposit_paket_kelas + $bonus_deposit_paket_kelas;
                    }else if($minimal_deposit == 10){
                        $id_promo = $request->id_promo;
                        $id_kelas = $request->id_kelas;
                        $id_member = $request->id_member;
                        $bonus_deposit_paket_kelas = $promo->bonus_deposit;
                        $masa_berlaku_deposit_kelas = Carbon::now()->addMonth()->addMonth();
                        $nominal_uang_deposit_paket_kelas = $kelas->harga_kelas * $nominal_deposit_paket_kelas;
                        $total_deposit_paket_kelas = $total_deposit_paket_kelas + $bonus_deposit_paket_kelas;
                    }
                }else{
                    $id_promo = null;
                    $id_kelas = $request->id_kelas;
                    $id_member = $request->id_member;
                    $bonus_deposit_paket_kelas = 0;
                    $masa_berlaku_deposit_kelas = null;
                    $nominal_deposit_paket_kelas = $request->nominal_deposit_paket_kelas;
                    $nominal_uang_deposit_paket_kelas = $kelas->harga_kelas * $nominal_deposit_paket_kelas;
                    $total_deposit_paket_kelas = $nominal_deposit_paket_kelas;
                }
            }else{
                $kelas = Kelas::findorfail($request->id_kelas);
                $id_kelas = $request->id_kelas;
                $id_member = $request->id_member;
                $bonus_deposit_paket_kelas = 0;
                $masa_berlaku_deposit_kelas = null;
                $nominal_deposit_paket_kelas = $request->nominal_deposit_paket_kelas;
                $nominal_uang_deposit_paket_kelas = $kelas->harga_kelas * $nominal_deposit_paket_kelas;
                $total_deposit_paket_kelas = $nominal_deposit_paket_kelas;
            }
            $cek = DepositKelas::where('id_member', $id_member)
            ->where('id_kelas', $id_kelas)
            ->value('deposit_kelas.id');
            if(is_null($cek)){
                $transaksipaketkelas = TransaksiDepositPaketKelas::firstOrCreate([
                    'id_pegawai' => $request->id_pegawai,
                    'id_member'=> $id_member,
                    'id_promo' => $id_promo,
                    'id_kelas' => $id_kelas,
                    'tanggal_deposit_paket_kelas' => date('Y-m-d H:i:s', strtotime('now')),
                    'nominal_deposit_paket_kelas' => $nominal_deposit_paket_kelas,
                    'nominal_uang_deposit_paket_kelas' => $nominal_uang_deposit_paket_kelas,
                    'bonus_deposit_paket_kelas' => $bonus_deposit_paket_kelas,
                    'masa_berlaku_deposit_kelas' => $masa_berlaku_deposit_kelas,
                    'total_deposit_paket_kelas' => $total_deposit_paket_kelas,
                ]);
                //Update data di tabel member
                //cari data member
                $member = Member::find($request->id_member);
                $pegawai = Pegawai::find($request->id_pegawai);
                // $cekDeposit = DepositKelas::where('id_member', $transaksipaketkelas->id_member)
                // ->where('id_kelas', $transaksipaketkelas->id_kelas)
                // ->value('deposit_kelas.id');
                $deposit = new DepositKelas();
                $deposit->id_member = $transaksipaketkelas->id_member;
                $deposit->id_kelas = $transaksipaketkelas->id_kelas;
                $deposit->sisa_deposit_kelas = $transaksipaketkelas->total_deposit_paket_kelas;
                $deposit->masa_berlaku_deposit_kelas = $transaksipaketkelas->masa_berlaku_deposit_kelas;
                $deposit->save();
                // if(is_null($cekDeposit)){
                //     $deposit = new DepositKelas();
                //     $deposit->id_member = $transaksipaketkelas->id_member;
                //     $deposit->id_kelas = $transaksipaketkelas->id_kelas;
                //     $deposit->sisa_deposit_kelas = $transaksipaketkelas->total_deposit_paket_kelas;
                //     $deposit->masa_berlaku_deposit_kelas = $transaksipaketkelas->masa_berlaku_deposit_kelas;
                //     $deposit->save();
                //     // $deposit -> save([
                //     //     'id_member' => $id_member,
                //     //     'id_kelas' => $id_kelas,
                //     //     'sisa_deposit_kelas' => $total_deposit_paket_kelas,
                //     //     'masa_berlaku_deposit_kelas' => $masa_berlaku_deposit_kelas
                //     // ]);
                // }else{
                //     $deposit = DepositKelas::find($cekDeposit);
                //     $deposit -> update([
                //         'sisa_deposit_kelas' => $total_deposit_paket_kelas,
                //         'masa_berlaku_deposit_kelas' => $masa_berlaku_deposit_kelas
                //     ]);
                // }
                return response([
                    'message'=> 'Transaksi Deposit Paket Kelas Berhasil',
                    'data' => ['transaksi_deposit_paket_kelas' => $transaksipaketkelas, 'deposit_kelas' => $deposit, 'sisa_deposit_kelas' => $deposit->sisa_deposit_kelas, 'nomor_struk_transaksi_deposit_paket_kelas' => TransaksiDepositPaketKelas::latest()->first()->nomor_struk_transaksi_deposit_paket_kelas, 'nama_member' => $member->nama_member, 'nomor_member' => $member->nomor_member, 'nama_pegawai' => $pegawai->nama_pegawai],
                    'total' => $total_deposit_paket_kelas,
                ]);
            }else{
                $depositKelas = DepositKelas::find($cek);
                $sisa = $depositKelas->sisa_deposit_kelas;
                if($sisa == 0){
                    $transaksipaketkelas = TransaksiDepositPaketKelas::firstOrCreate([
                        'id_pegawai' => $request->id_pegawai,
                        'id_member'=> $id_member,
                        'id_promo' => $id_promo,
                        'id_kelas' => $id_kelas,
                        'tanggal_deposit_paket_kelas' => date('Y-m-d H:i:s', strtotime('now')),
                        'nominal_deposit_paket_kelas' => $nominal_deposit_paket_kelas,
                        'nominal_uang_deposit_paket_kelas' => $nominal_uang_deposit_paket_kelas,
                        'bonus_deposit_paket_kelas' => $bonus_deposit_paket_kelas,
                        'masa_berlaku_deposit_kelas' => $masa_berlaku_deposit_kelas,
                        'total_deposit_paket_kelas' => $total_deposit_paket_kelas,
                    ]);
                    //Update data di tabel member
                    //cari data member
                    $member = Member::find($request->id_member);
                    $pegawai = Pegawai::find($request->id_pegawai);
                    $depositKelas -> update([
                        'sisa_deposit_kelas' => $total_deposit_paket_kelas,
                        'masa_berlaku_deposit_kelas' => $masa_berlaku_deposit_kelas
                    ]);
                    // $cekDeposit = DepositKelas::where('id_member', $transaksipaketkelas->id_member)
                    // ->where('id_kelas', $transaksipaketkelas->id_kelas)
                    // ->value('deposit_kelas.id');
                    // if(is_null($cekDeposit)){
                    //     $deposit = new DepositKelas();
                    //     $deposit->id_member = $transaksipaketkelas->id_member;
                    //     $deposit->id_kelas = $transaksipaketkelas->id_kelas;
                    //     $deposit->sisa_deposit_kelas = $transaksipaketkelas->total_deposit_paket_kelas;
                    //     $deposit->masa_berlaku_deposit_kelas = $transaksipaketkelas->masa_berlaku_deposit_kelas;
                    //     $deposit->save();
                    // }else{
                        // $deposit = DepositKelas::find($cekDeposit);
                        // $deposit -> update([
                        //     'sisa_deposit_kelas' => $total_deposit_paket_kelas,
                        //     'masa_berlaku_deposit_kelas' => $masa_berlaku_deposit_kelas
                        // ]);
                    // }
                    return response([
                        'message'=> 'Transaksi Deposit Paket Kelas Berhasil',
                        'data' => ['transaksi_deposit_paket_kelas' => $transaksipaketkelas, 'deposit_kelas' => $depositKelas, 'sisa_deposit_kelas' => $depositKelas->sisa_deposit_kelas, 'nomor_struk_transaksi_deposit_paket_kelas' => TransaksiDepositPaketKelas::latest()->first()->nomor_struk_transaksi_deposit_paket_kelas, 'nama_member' => $member->nama_member, 'nomor_member' => $member->nomor_member, 'nama_pegawai' => $pegawai->nama_pegawai],
                        'total' => $total_deposit_paket_kelas,
                    ]);
                }else{
                    return response(
                        ['message'=> 'Transaksi Hanya Dapat Dilakukan Jika Sisa Deposit 0',] , 400);
                }
            }

            

        } catch(Exception $e){
            dd($e);
        }
    }
}
