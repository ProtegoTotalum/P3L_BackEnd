<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PromoResource;

class PromoController extends Controller
{
    public function index()
    {
        //get promo
        $promo = Promo::latest()->get();
        //render view with posts
        if(count($promo) > 0){
            return new PromoResource(true, 'List Data Promo',
            $promo); // return data semua promo dalam bentuk json
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); // return message data promo kosong

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'jenis_promo' => 'required',
            'deskripsi_promo' => 'required',
            'minimal_deposit' => 'required',
            'bonus_deposit' => 'required',
        ]);

        if($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $promo = Promo::create([ 
            'jenis_promo' => $request->jenis_promo, 
            'deskripsi_promo' => $request->deskripsi_promo,
            'minimal_deposit' => $request->minimal_deposit,
            'bonus_deposit' => $request->bonus_deposit
        ]);

        return new PromoResource(true, 'Data Promo Berhasil Ditambahkan!', $promo);
    }

    public function destroy($id)
    {
        $promo= Promo::find($id);

        if(is_null($promo)){
            return response([
                'message' => 'Promo Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($promo->delete()){
            return response([
                'message' =>'Delete Promo Sukses',
                'data' => $promo
            ], 200);
        }
        return response([
            'message' => 'Delete Promo Gagal',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $promoIds = explode(',', $id);
        $promo= Promo::whereIn('id', $promoIds)->get();

        if($promo->count() > 0){
            return response([
                'message' => 'Data Promo Ditemukan',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Data Promo Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data promo tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_promo' => 'required',
            'deskripsi_promo' => 'required',
            'minimal_deposit' => 'required',
            'bonus_deposit' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $promo = Promo::find($id);
        $promo->update([
            'jenis_promo' => $request->jenis_promo, 
            'deskripsi_promo' => $request->deskripsi_promo,
            'minimal_deposit' => $request->minimal_deposit,
            'bonus_deposit' => $request->bonus_deposit
        ]);
        // alihkan halaman ke halaman departemen
        return new PromoResource(true, 'Data Promo Berhasil Diupdate!', $promo);
    }
}
