<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingGym;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookingGymResource;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function aktivitasGymBulanan(Request $request)
    {
        // date
        $bulan = Carbon::now()->month;
        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }

        // Tanggal Cetak
        $tanggalCetak = Carbon::now()->format('Y-m-d');
        $laporanGym = BookingGym::where('tanggal_pelaksanaan_gym', '<', $tanggalCetak)
            ->where('status_presensi_gym', "Hadir")
            ->whereMonth('tanggal_pelaksanaan_gym', $bulan)
            ->get()
            ->groupBy(function ($item) {
                // group by tanggal
                $carbonDate = Carbon::createFromFormat('Y-m-d', $item->tanggal_pelaksanaan_gym);
                return $carbonDate->toDateString();
            });

        // Count
        $responseData = [];

        foreach ($laporanGym as $tanggal => $grup) {
            $count = $grup->count();
            $responseData[] = [
                'tanggal' => $tanggal,
                'count' => $count,
            ];
        }

        return response([
            'data' => $responseData,
            'tanggal_cetak' => $tanggalCetak
        ]);
    }

    public function aktivitasKelasBulanan(Request $request)
    {
        $bulan = Carbon::now()->month;
        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }
        // dd($bulan);
        //* Tanggal Cetak
        $tanggalCetak = Carbon::now()->format('Y-m-d');
        
        $laporanKelas = DB::table('jadwal_harians')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
        ->leftJoin('booking_kelas', 'jadwal_harians.id', '=', 'booking_kelas.id_jadwal_harian')
        ->select(
            'kelas.nama_kelas as nama_kelas',
            'instrukturs.nama_instruktur as nama_instruktur',
            DB::raw('COUNT(DISTINCT booking_kelas.nomor_booking_kelas) AS jumlah_peserta_kelas'),
            DB::raw('COUNT(DISTINCT CASE WHEN jadwal_harians.status_jadwal_harian = "Libur" THEN jadwal_harians.id ELSE NULL END) AS jumlah_libur')
        )
        ->whereRaw('MONTH(jadwal_harians.tanggal_jadwal_harian) = ?', [$bulan])
        ->groupBy('kelas.nama_kelas', 'instrukturs.nama_instruktur')
        ->get();
    
        //akumulasi terlambat direset tiap bulan jam mulai tiap bulan - jam selesai bulan 
        
        return response([
            'data' => $laporanKelas,
            'tanggal_cetak' => $tanggalCetak,
        ]);
        
    }

    public function kinerjaInstrukturBulanan(Request $request)    {
        $bulan = Carbon::now()->month;
        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }
        // dd($bulan);
        //* Tanggal Cetak
        $tanggalCetak = Carbon::now()->format('Y-m-d');
        $kinerjaInstruktur = DB::select('
        SELECT i.nama_instruktur,
            SUM(CASE WHEN pi.id IS NOT NULL AND pi.status_presensi = "Masuk" THEN 1 ELSE 0 END) AS jumlah_hadir,
            SUM(CASE WHEN ij.id IS NOT NULL AND ij.status_konfirmasi = "Dikonfirmasi" THEN 1 ELSE 0 END) AS jumlah_ijin,
            IFNULL(i.jumlah_keterlambatan_instruktur, 0) AS jumlah_keterlambatan_instruktur
        FROM instrukturs AS i
        LEFT JOIN presensi_instrukturs AS pi ON i.id = pi.id_instruktur AND MONTH(pi.created_at) = ?
        LEFT JOIN ijin_instrukturs AS ij ON i.id = ij.id_instruktur AND MONTH(ij.created_at) = ?
        GROUP BY i.nama_instruktur, i.jumlah_keterlambatan_instruktur
    ', [$bulan, $bulan]);   
        return response([
            'data' => $kinerjaInstruktur,
            'tanggal_cetak' => $tanggalCetak,
        ]);
    }
}
