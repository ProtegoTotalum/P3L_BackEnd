<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\KelasResource;

class KelasController extends Controller
{
    public function index()
    {
        //get kelas
        $kelas = Kelas::latest()->get();
        //render view with posts
        if(count($kelas) > 0){
            return new KelasResource(true, 'List Data Kelas',
            $kelas); // return data semua kelas dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data kelas kosong
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required',
            'harga_kelas' => 'required',
            'kapasitas_kelas' => 'required',
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $kelas = Kelas::create([ 
            'nama_kelas' => $request->nama_kelas,
            'harga_kelas' => $request->harga_kelas, 
            'kapasitas_kelas' => $request->kapasitas_kelas,
        ]);

        return new KelasResource(true, 'Data Kelas Berhasil Ditambahkan!', $kelas);
    }

    public function destroy($id)
    {
        $kelas= Kelas::find($id);

        if(is_null($kelas)){
            return response([
                'message' => 'Kelas Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($kelas->delete()){
            return response([
                'message' =>'Delete Kelas Sukses',
                'data' => $kelas
            ], 200);
        }
        return response([
            'message' => 'Delete Kelas Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $kelas= Kelas::find($id);

        if(!is_null($kelas)){
            return response([
                'message' => 'Data Kelas Ditemukan',
                'data' => $kelas
            ], 404);
        }

        return response([
            'message' => 'Data Kelas Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data kelas tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required',
            'harga_kelas' => 'required',
            'kapasitas_kelas' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kelas = Kelas::find($id);
        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'harga_kelas' => $request->harga_kelas, 
            'kapasitas_kelas' => $request->kapasitas_kelas,
        ]);
        // alihkan halaman ke halaman kelas
        return new KelasResource(true, 'Data Kelas Berhasil Diupdate!', $kelas);
    }
}
