<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingKelas;
use App\Models\JadwalHarian;
use App\Models\Member;
use App\Models\Kelas;
use App\Models\Instruktur;
use App\Models\DepositKelas;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookingKelasResource;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class BookingKelasController extends Controller
{
    public function index()
    {
        //get booking kelas
        $bookkelas =  BookingKelas::with(['jadwalharian.jadwalumum.kelas','member', 'depositkelas', 'jadwalharian.instruktur'])->get();
        //render view with posts
        // $bookkelas = DB::table('booking_kelas')
        // ->join('members', 'booking_kelas.id_member', '=', 'members.id')
        // ->join('jadwal_harians', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harians.id')
        // ->join('deposit_kelas', 'booking_kelas.id_deposit_kelas', '=', 'deposit_kelas.id')
        // ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        // ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        // ->select(
        //     'booking_kelas.nomor_booking_kelas as nomor_booking_kelas',
        //     'booking_kelas.id_member as id_member',
        //     'booking_kelas.id_jadwal_harian as id_jadwal_harian',
        //     'booking_kelas.id_deposit_kelas as id_deposit_kelas',
        //     'members.nomor_member as nomor_member',
        //     'members.nama_member as nama_member',
        //     'kelas.nama_kelas as nama_kelas',
        //     'jadwal_harians.tanggal_jadwal_harian as tanggal_jadwal_harian',
        //     'booking_kelas.tanggal_booking_kelas as tanggal_booking_kelas',
        //     'booking_kelas.metode_pembayaran_booking_kelas as metode_pembayaran',
        //     'members.sisa_deposit_reguler as sisa_deposit_reguler',
        //     'deposit_kelas.sisa_deposit_kelas as sisa_deposit_kelas',
        //     'deposit_kelas.masa_berlaku_deposit_kelas as masa_berlaku_deposit_kelas',
        //     'booking_kelas.jam_presensi_kelas as jam_presensi_kelas',
        // )
        // ->get();
        if(count($bookkelas) > 0){
            return new BookingKelasResource(true, 'List Data Booking Kelas',
            $bookkelas); // return data semua booking kelas dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data booking kelas kosong
    }

    public function store(Request $request){
        try
        {
            $id_member = $request->id_member;
            $id_jadwal_harian = $request->id_jadwal_harian;
            $member = Member::find($id_member);
            $harian = JadwalHarian::find($id_jadwal_harian);
            $instruktur = Instruktur::find($harian->id_instruktur);
            $id_kelas = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
            ->where('jadwal_harians.id', $id_jadwal_harian)
            ->value('jadwal_umums.id_kelas');
            // $harga = Kelas::where('id', $id_kelas)
            //     ->value('harga_kelas');
            // $kapasitas = Kelas::where('id', $id_kelas)
            //     ->value('kapasitas_kelas');    
            $kelas = Kelas::find($id_kelas);
            $metode_pembayaran = $request->metode_pembayaran_booking_kelas;
            if($member->status_member == "Tidak Aktif"){
                return response(
                    ['message'=> 'Maaf Status Member Anda Tidak Aktif',] , 400);
            }else{
                if($harian->kapasitas_kelas == 0){
                    return response(
                        ['message'=> 'Maaf Kelas Penuh',] , 400);
                }else{
                    if($metode_pembayaran == "Reguler"){
                        if($member->sisa_deposit_reguler < $kelas->harga_kelas){
                            return response(
                                ['message'=> 'Maaf Saldo Anda Tidak Mencukupi',] , 400);
                        }else {
                            $booking = BookingKelas::firstOrCreate  ([
                                'id_jadwal_harian' => $id_jadwal_harian,
                                'id_member'=> $id_member,
                                'tanggal_booking_kelas' => date('Y-m-d H:i', strtotime('now')),
                                'metode_pembayaran_booking_kelas' => $metode_pembayaran,
                            ]);
                            //Update sisa deposit member 
                            $before = $member->sisa_deposit_reguler;
                            $member->sisa_deposit_reguler = $before - $kelas->harga_kelas;
                            $member->save();
                            //Update sisa kapasitas kelas di jadwal harian
                            $min = $harian->kapasitas_kelas - 1;
                            $harian->kapasitas_kelas = $min;
                            $harian->save(); 
                            return response([
                                'message'=> 'Booking Kelas Berhasil',
                                'data' => [ 'booking_kelas' => $booking, 
                                            'nomor_booking_kelas' => BookingKelas::latest()->first()->nomor_booking_kelas, 
                                            'nama_member' => $member->nama_member, 
                                            'nomor_member' => $member->nomor_member, 
                                            'nama_kelas' => $kelas->nama_kelas,
                                            'nama_instruktur' => $instruktur->nama_instruktur,
                                            'harga_kelas'  => $kelas->harga_kelas,
                                            'sisa_deposit'  => $member->sisa_deposit_reguler]
                            ]);
                        }
                    }else{
                        $id_deposit = DepositKelas::where('id_member', $id_member)
                        ->where('id_kelas', $id_kelas)
                        ->value('deposit_kelas.id');
                        
                        if(is_null($id_deposit)){
                            return response(
                                ['message'=> 'Anda Tidak Mempunyai Deposit Untuk Kelas Ini',] , 400);
                        }else{
                            $deposit = DepositKelas::find($id_deposit);
                            if($deposit->sisa_deposit_kelas == 0){
                                return response(
                                    ['message'=> 'Maaf Deposit Kelas Anda Tidak Mencukupi',] , 400);
                            }else{
                                $booking = BookingKelas::firstOrCreate  ([
                                    'id_jadwal_harian' => $id_jadwal_harian,
                                    'id_member'=> $id_member,
                                    'id_deposit_kelas' => $id_deposit,
                                    'tanggal_booking_kelas' => date('Y-m-d H:i', strtotime('now')),
                                    'metode_pembayaran_booking_kelas' => $metode_pembayaran,
                                ]);
                                //Update sisa deposit kelas member
                                $minDK = $deposit->sisa_deposit_kelas -1;
                                $deposit->sisa_deposit_kelas = $minDK;
                                $deposit->save();
                                //Update sisa kapasitas kelas di jadwal harian
                                $min = $harian->kapasitas_kelas - 1;
                                $harian->kapasitas_kelas = $min;
                                $harian->save(); 
                                return response([
                                    'message'=> 'Booking Kelas Berhasil',
                                    'data' => [ 'booking_kelas' => $booking, 
                                                'nomor_booking_kelas' => BookingKelas::latest()->first()->nomor_booking_kelas, 
                                                'nama_member' => $member->nama_member, 
                                                'nomor_member' => $member->nomor_member, 
                                                'nama_kelas' => $kelas->nama_kelas,
                                                'nama_instruktur' => $instruktur->nama_instruktur,
                                                'sisa_deposit_kelas'  => $deposit->sisa_deposit_kelas,
                                                'masa_berlaku'  => $deposit->masa_berlaku_deposit_kelas]
                                ]); 
                            }
                        }
                    }
                }
            }

        } catch(Exception $e){
            dd($e);
        }
    }

    public function destroy($id)
    {
        $booking = BookingKelas::find($id);

        if(is_null($booking)){
            return response([
                'message' => 'Booking Kelas Tidak Ditemukan',
                'data' => null
            ], 404);
        }
        $tanggal_booking = $booking->tanggal_booking;
        $currentDate = Carbon::now();

        if(Carbon::parse($tanggal_booking)->isBefore($currentDate)){
            if($booking->delete()){
                return response([
                    'message' =>'Sukses Membatalkan Booking',
                    'data' => $booking
                ], 200);
            }
            return response([
                'message' => 'Booking Hanya Dapat Dibatalkan H-1',
                'data' => null
            ], 400);
        }
    }
}
