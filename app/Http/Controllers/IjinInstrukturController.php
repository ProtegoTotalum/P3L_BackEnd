<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\IjinInstruktur;
use App\Models\JadwalHarian;
use App\Models\Instruktur;
use App\Models\Kelas;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\IjinInstrukturResource;
use App\Models\PresensiInstruktur;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IjinInstrukturController extends Controller
{
    public function index()
    {
        //get ijin instruktur
        $ijin =  IjinInstruktur::with(['instruktur','instrukturpengganti'])->get();
        // $ijin = DB::table('ijin_instrukturs')
        // ->join('instrukturs', 'ijin_instrukturs.id_instruktur', '=', 'instrukturs.id')
        // ->join('instrukturs', 'ijin_instrukturs.id_instruktur_pengganti', '=', 'instrukturs.id')
        // ->select('ijin_instrukturs.id as id', 'instrukturs.nama_instruktur as nama_instruktur', 'ijin_instrukturs.tanggal_pengajuan_ijin as tanggal_pengajuan_ijin' ,
        // 'ijin_instrukturs.tanggal_ijin_instruktur as tanggal_ijin_instruktur', 'ijin_instrukturs.hari_ijin as hari_ijin', 'ijin_instrukturs.sesi_ijin as sesi_ijin', 
        // 'ijin_instrukturs.alasan_ijin as alasan_ijin', 'instrukturs.nama_instruktur as nama_instruktur_pengganti', 'ijin_instrukturs.status_konfirmasi as status_konfirmasi')
        // ->get();
        //render view with posts
        // if(count($ijin) > 0){
        //     return new IjinInstrukturResource(true, 'List Data Ijin Instruktur',
        //     $ijin); // return data semua ijin instruktur dalam bentuk json
        // }


        // return response([
        //     'message' => 'Empty',
        //     'data' => null
        // ], 400); // return message data ijin instruktur kosong
        return new IjinInstrukturResource(true, 'List Data Ijin Instruktur',$ijin);
    }

    public function update(Request $request, $id){

        $ijin = IjinInstruktur::find($id);
        
        if(is_null($ijin)) {
        //data instruktur not found
        return new IjinInstrukturResource(false, 'Ijin Instruktur Tidak Ditemukan!', $ijin);
        }

        if($ijin){
            if($request->status_konfirmasi == "Ditolak"){
                $ijin->status_konfirmasi = $request->status_konfirmasi;
                $ijin->save();
                return new IjinInstrukturResource(false, 'Ijin Instruktur Ditolak', $ijin);
            }else{
                $harian = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
                ->where('jadwal_harians.id_instruktur', $ijin->id_instruktur)
                ->where('jadwal_umums.jam', $ijin->sesi_ijin)
                ->where('jadwal_harians.tanggal_jadwal_harian', $ijin->tanggal_ijin_instruktur)
                ->value('jadwal_harians.id');
                
                $nama_instruktur = Instruktur::where('id', $ijin->id_instruktur)
                ->value('nama_instruktur');
        
                $pengganti = Instruktur::where('id', $ijin->id_instruktur_pengganti)
                ->value('nama_instruktur');

                $ijin->status_konfirmasi = "Dikonfirmasi";
                $ijin->save();
                
                $jadwal_harian = JadwalHarian::find($harian);
                // $jadwal_harian->status_jadwal_harian = "Instruktur Pengganti";
                // $jadwal_harian->id_instruktur = $ijin->id_instruktur_pengganti;
                $jadwal_harian->update([
                    'id_instruktur' => $ijin->id_instruktur_pengganti,
                    'status_jadwal_harian' => $nama_instruktur . ' digantikan dengan ' . $pengganti
                ]);

                $presensi = PresensiInstruktur::where('id_jadwal_harian', $harian)
                ->value('presensi_instrukturs.id');

                $presensi_instruktur = PresensiInstruktur::find($presensi);

                $presensi_instruktur->update([
                    'id_instruktur' => $ijin->id_instruktur_pengganti,
                ]);
                
                return new IjinInstrukturResource(true, 'Ijin Instruktur Sudah Dikonfirmasi', $ijin);

                // $update = JadwalHarian::where('id', $jadwal_harian)->update([
                //     'ID_INSTRUKTUR'  => $izin->ID_INSTRUKTUR_PENGGANTI,
                //     'STATUS_JADWAL_HARIAN' => $nama_instruktur . ' digantikan dengan ' . $pengganti
                // ]);
    

                // $harian = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
                // ->where('jadwal_harians.id_instruktur', $ijin->id_instruktur)
                // ->where('jadwal_umums.jam', $ijin->sesi_ijin)
                // ->where('jadwal_harians.tanggal_jadwal_harian', $ijin->tanggal_ijin_instruktur)
                // ->value('jadwal_harians.id');

                // $ijin->status_konfirmasi = "Dikonfirmasi";
                // $ijin->save();
                    
                // $nama_instruktur = Instruktur::where('id', $ijin->id_instruktur)
                // ->value('nama_instruktur');
        
                // $pengganti = Instruktur::where('id', $ijin->id_instruktur_pengganti)
                // ->value('nama_instruktur');
                    
                // $jadwal_harian = JadwalHarian::find($harian);
                // $jadwal_harian->update([
                //     'id_instruktur' => $ijin->id_instruktur_pengganti,
                //     'status_jadwal_harian' => $nama_instruktur . ' digantikan dengan ' . $pengganti
                // ]);

                // return new IjinInstrukturResource(true, 'Ijin Instruktur Sudah Dikonfirmasi', $ijin);
            }
            
        }

    }
    
    public function store(Request $request){

        $tanggalIjin = $request->tanggal_ijin_instruktur;
        $tanggalPengajuan = Carbon::now()->format('Y-m-d');
        $id_instruktur = $request->id_instruktur;
        $sesi_ijin = $request->sesi_ijin;
        $hari_ijin = $request->hari_ijin;
        

        $cek = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
                ->where('jadwal_harians.id_instruktur', $id_instruktur)
                ->where('jadwal_umums.hari', $hari_ijin)
                ->where('jadwal_umums.jam', $sesi_ijin)
                ->where('jadwal_harians.tanggal_jadwal_harian', $tanggalIjin)
                ->value('jadwal_harians.id');

        if($tanggalPengajuan > $tanggalIjin ){
            return response(
                ['message'=> 'Maaf Ijin Hanya Dapat Diajukan H-1'] , 400);
        }else{
            if(is_null($cek)){
                return response(
                    ['message'=> 'Maaf Jadwal Kelas Tidak Tersedia'] , 400);
            }else{
                $validator = Validator::make($request->all(), [
                    'id_instruktur' => 'required',
                    'tanggal_ijin_instruktur' => 'required',
                    'hari_ijin' => 'required',
                    'sesi_ijin' => 'required',
                    'alasan_ijin' => 'required',
                    'id_instruktur_pengganti' => 'required',
                ]);
        
                if($validator->fails()) {
                    return response(['message' => $validator->errors()], 400);
                }

                $ijin = IjinInstruktur::create([ 
                    'id_instruktur' => $id_instruktur,
                    'tanggal_pengajuan_ijin' => $tanggalPengajuan, 
                    'tanggal_ijin_instruktur' => $tanggalIjin,
                    'hari_ijin' => $hari_ijin,
                    'sesi_ijin' => $sesi_ijin,
                    'alasan_ijin' => $request->alasan_ijin,
                    'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
                    'status_konfirmasi' => "Belum Dikonfirmasi",
                ]);

                return response([
                    'success' => 'true',
                    'message'=> 'Berhasil Menambahkan Ijin',
                    'data' => $ijin,
                ]);
            }
        }
    }

    public function getIjinInstruktur($id){

        // $ijin = IjinInstruktur::where('id_instruktur', $id)
        // ->value('ijin_instrukturs.id');

        // $harian = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        // ->where('jadwal_harians.id_instruktur', $ijin->id_instruktur)
        // ->where('jadwal_umums.hari', $ijin->hari_ijin)
        // ->where('jadwal_umums.jam', $ijin->sesi_ijin)
        // ->where('jadwal_harians.tanggal_jadwal_harian', $ijin->tanggal_pengajuan_ijin)
        // ->value('jadwal_harians.id');

        // $id_kelas = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        // ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        // ->where('jadwal_harians.id', $harian)
        // ->value('jadwal_umums.id_kelas'); 

        // $kelas = Kelas::find($id_kelas);

        $getIjin = DB::table('ijin_instrukturs')
        ->join('instrukturs', 'ijin_instrukturs.id_instruktur', '=', 'instrukturs.id')
        ->join('instrukturs as instruktur_pengganti', 'ijin_instrukturs.id_instruktur_pengganti', '=', 'instruktur_pengganti.id')
        ->select(
            'ijin_instrukturs.id as id_ijin',
            'ijin_instrukturs.id_instruktur as id_instruktur',
            'instrukturs.nama_instruktur as nama_instruktur',
            'ijin_instrukturs.tanggal_pengajuan_ijin as tanggal_pengajuan_ijin',
            'ijin_instrukturs.tanggal_ijin_instruktur as tanggal_ijin_instruktur',
            'ijin_instrukturs.hari_ijin as hari_ijin',
            'ijin_instrukturs.sesi_ijin as sesi_ijin',
            'ijin_instrukturs.alasan_ijin as alasan_ijin',
            'ijin_instrukturs.id_instruktur_pengganti as id_instruktur_pengganti',
            'instruktur_pengganti.nama_instruktur as nama_instruktur_pengganti',
            'ijin_instrukturs.status_konfirmasi as status_konfirmasi'
        )
        ->where('ijin_instrukturs.id_instruktur', '=', $id)
        ->get();
        if(count($getIjin) > 0){
            return new IjinInstrukturResource(true, 'List Data Ijin Instruktur',
            $getIjin); // return data semua ijin instruktur dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data ijin instruktur kosong
    }


}
