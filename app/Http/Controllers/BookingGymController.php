<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingGym;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookingGymResource;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class BookingGymController extends Controller
{
    public function index()
    {
        //get booking gym
        //$bookgym =  BookingGym::with(['member'])->get();
        //render view with posts
        $bookgym = DB::table('booking_gyms')
        ->join('members', 'booking_gyms.id_member', '=', 'members.id')
        ->select(
            'booking_gyms.nomor_booking_gym as nomor_booking_gym',
            'booking_gyms.id_member as id_member',
            'members.nomor_member as nomor_member',
            'members.nama_member as nama_member',
            'booking_gyms.tanggal_booking_gym as tanggal_booking_gym',
            'booking_gyms.tanggal_pelaksanaan_gym as tanggal_pelaksanaan_gym',
            'booking_gyms.jam_sesi_booking_gym as jam_sesi_booking_gym',
            'booking_gyms.kapasitas_gym as kapasitas-gym',
            'booking_gyms.jam_presensi_gym as jam_presensi_gym',
        )
        ->get();
        if(count($bookgym) > 0){
            return new BookingGymResource(true, 'List Data Booking Gym',
            $bookgym); // return data semua booking gym dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data booking gym kosong
    }

    public function store(Request $request){
        try
        {
            $id_member = $request->id_member;
            $member = Member::find($id_member);
            $pelaksanaan = $request->tanggal_pelaksanaan_gym;
            // $gym = BookingGym::where('tanggal_pelaksanaan_gym', $pelaksanaan)
            //     ->where('jam_sesi_booking_gym', $jam_sesi)
            //     ->value('booking_gyms.id');
            $selected = Carbon::parse($pelaksanaan);
            $current = Carbon::now();
            if($selected->lte($current)){
                return response(
                    ['message'=> 'Maaf Gym Hanya Dapat Dipesan H-1'] , 400);
            }else{
                if($member->status_member == "Tidak Aktif"){
                    return response(
                        ['message'=> 'Maaf Status Member Anda Tidak Aktif',] , 400);
                }else{
                    $cek = BookingGym::where('id_member', $id_member)->value('booking_gyms.tanggal_pelaksanaan_gym');
    
                    if($pelaksanaan == $cek){
                        return response(
                        ['message'=> 'Maaf Gym Hanya Dapat Dilakukan 1 Sesi per Hari'] , 400);
                    }else{
                        // $kapasitas = 10;

                        $booking = new BookingGym();
                        $booking->id_member = $request->id_member;
                        $currentDateTime = Carbon::now();
                        $tanggal_booking_gym = $currentDateTime->format('Y-m-d H:i');
                        $booking->tanggal_booking_gym = $tanggal_booking_gym;

                        $booking->tanggal_pelaksanaan_gym = $pelaksanaan;
                        $booking->jam_sesi_booking_gym = $request->jam_sesi_booking_gym;
                        // $booking->kapasitas_gym = $kapasitas;

                        $jamSesiOptions = ['7-9', '9-11', '11-13', '13-15', '15-17', '17-19', '19-21'];

                        if (in_array($booking->jam_sesi_booking_gym, $jamSesiOptions)) {
                            $kapasitas= BookingGym::where('tanggal_pelaksanaan_gym', $booking->tanggal_pelaksanaan_gym)
                                ->where('jam_sesi_booking_gym', $booking->jam_sesi_booking_gym)
                                ->count();
                    
                            if ($kapasitas < 10) {
                                $newKapasitas = $kapasitas + 1;
                                $booking->kapasitas_gym = $newKapasitas;
                                $booking->save();
                                
                                return response([
                                    'message'=> 'Booking Gym Berhasil',
                                    'data' => ['booking_gym' => $booking, 'nomor_booking_gym' => BookingGym::latest()->first()->nomor_booking_gym, 'nama_member' => $member->nama_member, 'nomor_member' => $member->nomor_member,],
                                ]);
                            } else {
                                return response(
                                    ['message'=> 'Maaf Gym Untuk Sesi Ini Sudah Penuh',] , 400);
                            }
                        } else {
                            return response(
                                ['message'=> 'Maaf Sesi Tidak Tersedia',] , 400);
                        }
                        // $isFull = $this->isGymFull($jam_sesi, $pelaksanaan);
                        // if($isFull){
                            // return response(
                            //     ['message'=> 'Maaf Untuk Gym Pada Tanggal' . $pelaksanaan . 'Sesi' . $jam_sesi . 'Sudah Penuh',] , 400);
                        // }else{
                        //     $bookingGym = BookingGym::firstOrCreate  ([
                        //         'id_member'=> $id_member,
                        //         'tanggal_booking_gym' => date('Y-m-d H:i:s', strtotime('now')),
                        //         'tanggal_pelaksanaan_gym' => $pelaksanaan,
                        //         'jam_sesi_booking_gym' => $jam_sesi,
                        //         'kapasitas' => $this->incrementKapasitasGym($jam_sesi, $pelaksanaan)
                        //     ]);

                        //     return new BookingGymResource(true, 'Booking Gym Berhasil Ditambahkan', $bookingGym);
                        // }
                    }
                }
            }
        } catch(Exception $e){
            dd($e);
        }
    }

    public function update($id)
    {
        $gym = BookingGym::find($id);
        $gym->jam_presensi_gym = date('H:i:s', strtotime('now'));
        $gym->status_presensi_gym = "Hadir";
        $gym->update();
        return new BookingGymResource(true, 'Berhasil Presensi Gym', $gym);

    }

    // private function incrementKapasitasGym($jamSesi, $tanggal){
    //     DB::table('booking_gyms')
    //         ->where('jam_sesi_booking_gym', $jamSesi)
    //         ->where('tanggal_pelaksanaan_gym', $tanggal)
    //         ->increment('kapasitas_gym');
    // }

    // private function isGymFull($jamSesi, $tanggal)
    // {
    //     $kapasitasGym = DB::table('booking_gyms')
    //         ->where('jam_sesi', $jamSesi)
    //         ->where('tanggal', $tanggal)
    //         ->sum('kapasitas_gym');
            
    //     return $kapasitasGym >= 10;
    // }
}
