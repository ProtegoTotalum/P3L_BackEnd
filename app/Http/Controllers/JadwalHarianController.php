<?php

namespace App\Http\Controllers;

use App\Http\Resources\JadwalHarianResource;
use App\Models\JadwalHarian;
use App\Models\PresensiInstruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class JadwalHarianController extends Controller
{
    public function index()
    {
        // $harian = JadwalHarian::with(['instruktur','jadwalumum'])->get();
        $harian = DB::table('jadwal_harians')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->select('jadwal_harians.id as id', 
        'jadwal_harians.tanggal_jadwal_harian as tanggal_jadwal_harian' ,
        'instrukturs.nama_instruktur as nama_instruktur', 
        'jadwal_harians.status_jadwal_harian as status', 
        'kelas.nama_kelas as nama_kelas',
        'kelas.harga_kelas as harga_kelas', 
        'jadwal_umums.hari as hari', 
        'jadwal_umums.jam as jam')
        ->orderBy('tanggal_jadwal_harian')
        ->get();
        return new JadwalHarianResource(true, 'Data Jadwal Harian',$harian);
    }

    public function store(){
        $cek = JadwalHarian::where('tanggal_jadwal_harian', '>', Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d'))->first();
        if(!is_null($cek)){
            return response()->json([
                'message'=> 'Jadwal Harian Sudah Di Generate',],400);
        }
        
        //generate tanggal
        $start_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDay();
        $end_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(7);
        
        //Mapping Hari
        $map = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];
        for($date = $start_date ; $date->lte($end_date);$date->addDay())
        {
            $hari = Carbon::parse($date)->format('l');
            $jadwal_umum = DB::table('jadwal_umums')
            ->where('jadwal_umums.hari','=',$map[strtolower($hari)])
            ->get();

            foreach($jadwal_umum as $jadwalumum){
                $jadwal_harian = DB::table('jadwal_harians')
                ->where('tanggal_jadwal_harian','=',$date->toDateString())
                ->where('id_jadwal_umum', '=', $jadwalumum->id)
                ->first();
                if(!$jadwal_harian){
                    DB::table('jadwal_harians')->insert([
                        'tanggal_jadwal_harian' =>$date->toDateString(),
                        'status_jadwal_harian' => 'Masuk',
                        'id_jadwal_umum' =>$jadwalumum->id,
                        'id_instruktur' =>$jadwalumum->id_instruktur,
                        'kapasitas_kelas' => '10'   
                    ]);
                }
            }
        }
        return response([
            'message'=> 'Berhasil Melakukan Generate',
        ]);
    }
    public function update($id_jadwal_harian){
        $jadwal_harian = JadwalHarian::find($id_jadwal_harian);
        $jadwal_harian->status_jadwal_harian = 'Libur';
        $jadwal_harian->update();

        $presensi = PresensiInstruktur::where('id_jadwal_harian', $jadwal_harian->id)
        ->get();
        foreach ($presensi as $presensiItem) {
            $presensiItem->status_presensi = "Libur";
            $presensiItem->save();
        }
        return response()->json(['message' => 'Jadwal Harian Berhasil Diliburkan'], 200);
    }
}
