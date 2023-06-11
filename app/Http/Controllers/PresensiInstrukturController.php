<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiInstruktur;
use App\Models\JadwalHarian;
use App\Models\Instruktur;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PresensiInstrukturResource;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class PresensiInstrukturController extends Controller
{
    public function index()
    {
        //get presensi instruktur
        //$presensi =  PresensiInstruktur::with(['instruktur', 'jadwalharian'])->get();
        //render view with posts
        $presensi = DB::table('presensi_instrukturs')
        ->join('jadwal_harians', 'presensi_instrukturs.id_jadwal_harian', '=', 'jadwal_harians.id')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'presensi_instrukturs.id_instruktur', '=', 'instrukturs.id')
        ->select(
            'presensi_instrukturs.id as id_presensi',
            'presensi_instrukturs.id_instruktur as id_instruktur',
            'instrukturs.nama_instruktur as nama_instruktur',
            'jadwal_harians.id as id_jadwal_harian',
            'jadwal_harians.tanggal_jadwal_harian as tanggal_jadwal_harian',
            'kelas.nama_kelas as nama_kelas',
            'presensi_instrukturs.jam_mulai_kelas as jam_mulai',
            'presensi_instrukturs.jam_selesai_kelas as jam_selesai',
            'presensi_instrukturs.status_presensi as status_presensi'
        )
        ->get();
        if(count($presensi) > 0){
            return new PresensiInstrukturResource(true, 'List Data Presensi Instruktur',
            $presensi); // return data semua presensi instruktur dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data presensi instruktur kosong
    }

    public function createPresensiInstruktur()
    {
        // Retrieve all JadwalHarian records
        $jadwalHarians = JadwalHarian::all();
        if($jadwalHarians == null){
            return response()->json(['message' => 'Jadwal Harian Belum Digenerate']);
        }else{
            $cek = PresensiInstruktur::count();
            if($cek == 0){
                foreach ($jadwalHarians as $jadwalHarian) {
                    // Create new PresensiInstruktur record
                   $presensi = PresensiInstruktur::create([
                        'id_instruktur' => $jadwalHarian->id_instruktur,
                        'id_jadwal_harian' => $jadwalHarian->id,
                        'jam_mulai_kelas' => null, 
                        'jam_selesai_kelas' => null, 
                    ]);
                }
        
                return response()->json(['message' => 'Berhasil Melakukan Generate Presensi Instruktur']);
            }else{
                return response()->json([
                    'message'=> 'Presensi Instruktur Sudah Di Generate',],400);
            }
        }
    }

    public function update(Request $request, $id){
        $presensi = PresensiInstruktur::find($id);

        $instruktur = Instruktur::find($presensi->id_instruktur);
        $id_jadwal_harian = JadwalHarian::where('id', $presensi->id_jadwal_harian)
            ->value('jadwal_harians.id');
        $harians = JadwalHarian::find($presensi->id_jadwal_harian);
        $takejam = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->where('jadwal_harians.id', $id_jadwal_harian)
            ->value('jadwal_umums.jam');

        if($presensi->jam_mulai_kelas == null){
            $format = 'H:i';
            try {
                $time = Carbon::createFromFormat($format, $takejam);
            } catch (\Exception $e) {
                // Handle error: Invalid time format
                return response()->json(['error' => 'Invalid time format'], 422);
            }

            // $jam_mulai = $request->jam_mulai_kelas;
            $jam_mulai = Carbon::createFromFormat($format, $request->jam_mulai_kelas);
            $validator = Validator::make($request->all(), [
                'jam_mulai_kelas' => 'required',
                ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $presensi->update([
                'jam_mulai_kelas' => $request->jam_mulai_kelas,
            ]);

            if($jam_mulai->greaterThan($time)){
                $telat = $instruktur->jumlah_keterlambatan_instruktur + 1;
                $instruktur->jumlah_keterlambatan_instruktur = $telat;
                $instruktur->save();
            }
        }else{
            $validator = Validator::make($request->all(), [
                'jam_selesai_kelas' => 'required',
                'status_presensi' => 'required',
                ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $presensi->update([
                'jam_selesai_kelas' => $request->jam_selesai_kelas,
                'status_presensi' => $request->status_presensi, 
            ]);
        }

        return new PresensiInstrukturResource(true, 'Data Presensi Berhasil Diupdate!', $presensi);
    }

    public function show($id)
    {
        $presensi = DB::table('presensi_instrukturs')
        ->join('jadwal_harians', 'presensi_instrukturs.id_jadwal_harian', '=', 'jadwal_harians.id')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'presensi_instrukturs.id_instruktur', '=', 'instrukturs.id')
        ->select(
            'presensi_instrukturs.id as id_presensi',
            'presensi_instrukturs.id_instruktur as id_instruktur',
            'instrukturs.nama_instruktur as nama_instruktur',
            'jadwal_harians.id as id_jadwal_harian',
            'jadwal_harians.tanggal_jadwal_harian as tanggal_jadwal_harian',
            'kelas.nama_kelas as nama_kelas',
            'presensi_instrukturs.jam_mulai_kelas as jam_mulai',
            'presensi_instrukturs.jam_selesai_kelas as jam_selesai',
            'presensi_instrukturs.status_presensi as status_presensi'
        )
        ->where('presensi_instrukturs.id', '=', $id)
        ->get();

        if(!is_null($presensi)){
            return response([
                'success' => true,
                'message' => 'Data Presensi Instruktur Ditemukan',
                'data' => $presensi
            ], 200);
        }

        return response([
            'message' => 'Data Presensi Instruktur Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data presensi instruktur tidak ditemukan
    }

    public function jamMulai($id)
    {
        $presensi = PresensiInstruktur::find($id);
        $instruktur = Instruktur::find($presensi->id_instruktur);
        $id_jadwal_harian = JadwalHarian::where('id', $presensi->id_jadwal_harian)
            ->value('jadwal_harians.id');
            
        $takejam = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
            ->where('jadwal_harians.id', $id_jadwal_harian)
            ->value('jadwal_umums.jam');

        $jam = date('H:i', strtotime('now'));
        if($jam > $takejam){
            $telat = $instruktur->jumlah_keterlambatan_instruktur + 1;
            $instruktur->jumlah_keterlambatan_instruktur = $telat;
            $instruktur->save();
        }
        $presensi->jam_mulai_kelas = $jam;
        $presensi->update();
        return response([
            'success' => true,
            'message' => 'Jam Mulai Kelas Berhasil di Update',
        ], 200);
    }

    public function jamSelesai($id)
    {
        $presensi = PresensiInstruktur::find($id);
        $presensi->jam_selesai_kelas = date('H:i', strtotime('now'));
        $presensi->status_presensi = "Masuk";
        $presensi->update();
        return response([
            'success' => true,
            'message' => 'Jam Selesai Kelas Berhasil di Update',
        ], 200);
    }

    public function getPresensiToday()
    {
        $presensi = DB::table('presensi_instrukturs')
        ->join('jadwal_harians', 'presensi_instrukturs.id_jadwal_harian', '=', 'jadwal_harians.id')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'presensi_instrukturs.id_instruktur', '=', 'instrukturs.id')
        ->select(
            'presensi_instrukturs.id as id_presensi',
            'presensi_instrukturs.id_instruktur as id_instruktur',
            'instrukturs.nama_instruktur as nama_instruktur',
            'jadwal_harians.id as id_jadwal_harian',
            'jadwal_harians.tanggal_jadwal_harian as tanggal_jadwal_harian',
            'kelas.nama_kelas as nama_kelas',
            'presensi_instrukturs.jam_mulai_kelas as jam_mulai',
            'presensi_instrukturs.jam_selesai_kelas as jam_selesai',
            'presensi_instrukturs.status_presensi as status_presensi'
        )
        ->where('jadwal_harians.tanggal_jadwal_harian', '=', date('Y-m-d'))
        ->get();
        if(count($presensi) > 0){
            return new PresensiInstrukturResource(true, 'List Data Presensi Instruktur',
            $presensi); // return data semua presensi instruktur dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data presensi instruktur kosong
    }
}
