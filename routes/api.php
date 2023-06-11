<?php

use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::post('logout', 'Api\AuthController@logout');

Route::get('email/verify/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

Route::put('reset/{id}', 'App\Http\Controllers\MemberController@resetPassword');

Route::get('/getpresensi/{id}', 'App\Http\Controllers\PresensiInstrukturController@show');
Route::get('getpresensi', 'App\Http\Controllers\PresensiInstrukturController@index');
Route::get('getjadwalharian', 'App\Http\Controllers\JadwalHarianController@index');
Route::get('getmember/{id}', 'App\Http\Controllers\MemberController@show');
Route::get('getinstruktur/{id}', 'App\Http\Controllers\InstrukturController@show');
Route::get('getinstruktur', 'App\Http\Controllers\InstrukturController@index');
Route::get('getdepositkelas/{id}', 'App\Http\Controllers\DepositKelasController@show');
Route::get('/jammulai/{id}', [App\Http\Controllers\PresensiInstrukturController::class, 'jamMulai']);
Route::get('/jamselesai/{id}', [App\Http\Controllers\PresensiInstrukturController::class, 'jamSelesai']);
Route::get('/presensitoday', [App\Http\Controllers\PresensiInstrukturController::class, 'getPresensiToday']);
Route::get('/getijininstruktur/{id}', [App\Http\Controllers\IjinInstrukturController::class, 'getIjinInstruktur']);
Route::get('/gethistorybookingkelas/{id}', [App\Http\Controllers\BookingKelasController::class, 'getHistoryBookingKelas']);
Route::get('/gethistorybookinggym/{id}', [App\Http\Controllers\BookingGymController::class, 'getHistoryBookingGym']);

Route::get('/laporanaktivitasgym',[App\Http\Controllers\LaporanController::class, 'aktivitasGymBulanan']);
Route::get('/laporanaktivitaskelas',[App\Http\Controllers\LaporanController::class, 'aktivitasKelasBulanan']);
Route::get('/laporankinerjainstruktur',[App\Http\Controllers\LaporanController::class, 'kinerjaInstrukturBulanan']);


Route::apiResource('/bookinggym', App\Http\Controllers\BookingGymController::class);
Route::apiResource('/bookingkelas', App\Http\Controllers\BookingKelasController::class);
Route::apiResource('/depositkelas', App\Http\Controllers\DepositKelasController::class);
Route::apiResource('/instruktur', App\Http\Controllers\InstrukturController::class);
Route::apiResource('/transaksireguler', App\Http\Controllers\TransaksiDepositRegulerController::class);
Route::apiResource('/transaksiaktivasi', App\Http\Controllers\TransaksiAktivasiController::class);
Route::apiResource('/transaksipaket', App\Http\Controllers\TransaksiDepositPaketKelasController::class);
Route::apiResource('/pegawai', App\Http\Controllers\PegawaiController::class);
Route::apiResource('/member', App\Http\Controllers\MemberController::class);
Route::apiResource('/kelas', App\Http\Controllers\KelasController::class);
Route::apiResource('/user', App\Http\Controllers\UserController::class);
Route::apiResource('/promo', App\Http\Controllers\PromoController::class);
Route::apiResource('/jadwalumum', App\Http\Controllers\JadwalUmumController::class);
Route::apiResource('/jadwalharian', App\Http\Controllers\JadwalHarianController::class);
Route::apiResource('/ijin', App\Http\Controllers\IjinInstrukturController::class);
Route::apiResource('/presensi', App\Http\Controllers\PresensiInstrukturController::class);

Route::get('/createpresensiinstruktur', [App\Http\Controllers\PresensiInstrukturController::class, 'createPresensiInstruktur']);
Route::get('/masaberlakumember', [App\Http\Controllers\SistemController::class, 'getMasaBerlakuMember']);
Route::get('/deaktivasimember', [App\Http\Controllers\SistemController::class, 'deaktivasiMember']);
Route::get('/masaberlakudepositkelas', [App\Http\Controllers\SistemController::class, 'getMasaBerlakuDepositKelas']);
Route::get('/resetdeposit', [App\Http\Controllers\SistemController::class, 'resetDepositKelas']);

// Route::group(['middleware' => 'auth:api'], function(){

// });