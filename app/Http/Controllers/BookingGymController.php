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
        $bookgym =  BookingGym::with(['member'])->get();
        //render view with posts
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
            if($member->status_member == "Tidak Aktif"){
                return response(
                    ['message'=> 'Maaf Status Member Anda Tidak Aktif',] , 400);
            }else{
                
            }

        } catch(Exception $e){
            dd($e);
        }
    }
}
