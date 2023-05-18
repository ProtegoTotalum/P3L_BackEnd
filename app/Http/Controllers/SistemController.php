<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Models\DepositKelas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;


class SistemController extends Controller
{
    public function getMasaBerlakuMember()
    {
        $today = Carbon::today();

        $members = Member::where('masa_berlaku_member', '<=', $today)
        ->whereNotNull('masa_berlaku_member')
        ->get();
        return response([
            'message'=>'Success Tampil Data',
            'data' => $members
        ],200); 
    }

    public function deaktivasiMember()
    {
        $today = Carbon::today();

        $members = Member::where('masa_berlaku_member', '<=', $today)
                          ->get();
        foreach ($members as $member) {
        $member->fill([
                'masa_berlaku_member' => null,
                'status_member' => "Tidak Aktif",                // add more attributes to reset to 0 as necessary
            ]);
        $member->save();
        }
        return response([
            'message'=>'Success Mendeaktivasi Member',
            'data' => $members
        ],200); 
    }

    public function getMasaBerlakuDepositKelas()
    {
        $today = Carbon::today();

        // $deposit = DepositKelas::where('masa_berlaku_deposit_kelas', '<', $today)->get();
        // return response([
        //     'message'=>'Success Tampil Data',
        //     'data' => $deposit
        // ],200); 
        $deposit = DB::table('deposit_kelas')
        ->join('members', 'deposit_kelas.id_member', '=', 'members.id')
        ->join('kelas', 'deposit_kelas.id_kelas', '=', 'kelas.id')
        ->select('deposit_kelas.id as id', 'members.nomor_member as nomor_member', 'members.nama_member as nama_member' ,
        'kelas.nama_kelas as nama_kelas', 'deposit_kelas.sisa_deposit_kelas as sisa_deposit_kelas', 'deposit_kelas.masa_berlaku_deposit_kelas as masa_berlaku_deposit_kelas')
        ->where('masa_berlaku_deposit_kelas', '<=', $today)
        ->get();
        return response([
            'message'=>'Success Tampil Data',
            'data' => $deposit
        ],200); 
    }

    public function resetDepositKelas()
    {
        $today = Carbon::today();

        $deposits = DepositKelas::where('masa_berlaku_deposit_kelas', '<=', $today)->get();

        foreach ($deposits as $deposit) {
        $deposit->fill([
                'sisa_deposit_kelas' => 0,
                'masa_berlaku_deposit_kelas' => null,
            ]);
        $deposit->save();
        }
        return response([
            'message'=>'Success Reset Deposit Kelas Member',
            'data' => $deposits
        ],200); 
    }
}
