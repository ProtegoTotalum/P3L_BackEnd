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
        $presensi =  PresensiInstruktur::with(['instruktur', 'jadwalharian'])->get();
        //render view with posts
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
            foreach ($jadwalHarians as $jadwalHarian) {
                // Create new PresensiInstruktur record
               $presensi = PresensiInstruktur::create([
                    'id_instruktur' => $jadwalHarian->id_instruktur,
                    'id_jadwal_harian' => $jadwalHarian->id,
                    'jam_mulai' => null, 
                    'jam_selesai' => null, 
                ]);
            }
    
            return response()->json(['message' => 'Berhasil Melakukan Generate Presensi Instruktur']);
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
}
