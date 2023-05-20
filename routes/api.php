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

Route::get('/masaberlakumember', [App\Http\Controllers\SistemController::class, 'getMasaBerlakuMember']);
Route::get('/deaktivasimember', [App\Http\Controllers\SistemController::class, 'deaktivasiMember']);
Route::get('/masaberlakudepositkelas', [App\Http\Controllers\SistemController::class, 'getMasaBerlakuDepositKelas']);
Route::get('/resetdeposit', [App\Http\Controllers\SistemController::class, 'resetDepositKelas']);

// Route::group(['middleware' => 'auth:api'], function(){

// });