<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiDepositPaketKelas extends Model
{
    use HasFactory;
    protected $primaryKey = 'nomor_struk_transaksi_deposit_paket_kelas';

    protected $fillable =[
        'nomor_struk_transaksi_deposit_paket_kelas',
        'id_pegawai',
        'id_member',
       //'id_deposit_kelas',
        'id_promo',
        'id_kelas',
        'tanggal_deposit_paket_kelas',
        'nominal_deposit_paket_kelas',
        'nominal_uang_deposit_paket_kelas',
        'bonus_deposit_paket_kelas',
        'masa_berlaku_deposit_kelas',
        'total_deposit_paket_kelas',
    ];

    protected $casts = [
        'nomor_struk_transaksi_deposit_paket_kelas' => 'string'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    public function pegawai(){
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
    }

    // public function depositkelas(){
    //     return $this->belongsTo(DepositKelas::class, 'id_deposit_kelas', 'id');
    // }

    public function member(){
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }

    public function promo(){
        return $this->belongsTo(Promo::class, 'id_promo', 'id');
    }

    public function kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
}
