<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\IjinInstruktur;
use App\Models\JadwalHarian;
use App\Models\Instruktur;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\IjinInstrukturResource;
use Illuminate\Support\Facades\DB;

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

                // $update = JadwalHarian::where('id', $jadwal_harian)->update([
                //     'ID_INSTRUKTUR'  => $izin->ID_INSTRUKTUR_PENGGANTI,
                //     'STATUS_JADWAL_HARIAN' => $nama_instruktur . ' digantikan dengan ' . $pengganti
                // ]);
        
                return new IjinInstrukturResource(true, 'Ijin Instruktur Sudah Dikonfirmasi', $ijin);

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


}
