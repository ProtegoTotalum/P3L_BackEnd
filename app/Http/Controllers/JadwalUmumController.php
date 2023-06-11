<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalUmum;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\JadwalUmumResource;

class JadwalUmumController extends Controller
{
    public function index()
    {
        //get jadwal umum
        $umum =  JadwalUmum::with(['instruktur','kelas'])->get();
        //render view with posts
        if(count($umum) > 0){
            return new JadwalUmumResource(true, 'List Data Jadwal Umum',
            $umum); // return data semua jadwal umum dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data jadwal umum kosong

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'id_kelas' => 'required',
            'hari' => 'required',
            'jam' => 'required',
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $cek = JadwalUmum::join('instrukturs','jadwal_umums.id_instruktur', '=', 'instrukturs.id')
            ->where('instrukturs.id', $request->id_instruktur)
            ->where('jadwal_umums.hari', $request->hari)
            ->where('jadwal_umums.jam', $request->jam)
            ->value('jadwal_umums.id');

        if(is_null($cek)){
            $jadwalUmum = JadwalUmum::create([ 
                'id_instruktur' => $request->id_instruktur,
                'id_kelas' => $request->id_kelas, 
                'hari' => $request->hari,
                'jam' => $request->jam,
            ]);

            return new JadwalUmumResource(true, 'Data Jadwal Umum Berhasil Ditambahkan!', $jadwalUmum);
        }else{
            return response(
                ['message'=> 'Jadwal Instruktur Bertabrakan'] , 400);
        }

    }

    public function destroy($id)
    {
        $jadwalUmum= JadwalUmum::find($id);

        if(is_null($jadwalUmum)){
            return response([
                'message' => 'Jadwal Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($jadwalUmum->delete()){
            return response([
                'message' =>'Delete Jadwal Sukses',
                'data' => $jadwalUmum
            ], 200);
        }
        return response([
            'message' => 'Delete Jadwal Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $jadwalUmum= JadwalUmum::find($id);

        if(!is_null($jadwalUmum)){
            return response([
                'message' => 'Jadwal Ditemukan',
                'data' => $jadwalUmum
            ], 404);
        }

        return response([
            'message' => 'Jadwal Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data jadwal tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'id_kelas' => 'required',
            'hari' => 'required',
            'jam' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $cek = JadwalUmum::join('instrukturs','jadwal_umums.id_instruktur', '=', 'instrukturs.id')
        ->where('instrukturs.id', $request->id_instruktur)
        ->where('jadwal_umums.hari', $request->hari)
        ->where('jadwal_umums.jam', $request->jam)
        ->value('jadwal_umums.id');

        if(is_null($cek)){
            $jadwalUmum = JadwalUmum::find($id);
            $jadwalUmum->update([
                'id_instruktur' => $request->id_instruktur,
                'id_kelas' => $request->id_kelas, 
                'hari' => $request->hari,
                'jam' => $request->jam,
            ]);
            // alihkan halaman ke halaman departemen
            return new JadwalUmumResource(true, 'Data Jadwal Umum Berhasil Diupdate!', $jadwalUmum);
        }else{
            return response(
                ['message'=> 'Jadwal Instruktur Bertabrakan'] , 400);
        }
    }
}
