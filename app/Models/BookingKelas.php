<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookingKelas extends Model
{
    use HasFactory;

    protected $primaryKey = 'nomor_booking_kelas';

    protected $fillable =[
        'nomor_booking_kelas',
        'id_jadwal_harian',
        'id_member',
        'id_deposit_kelas',
        'tanggal_booking_kelas',
        'metode_pembayaran_booking_kelas',
        'jam_presensi_kelas',
    ];

    protected $casts = [
        'nomor_booking_kelas' => 'string'
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

    public function jadwalharian(){
        return $this->belongsTo(JadwalHarian::class, 'id_jadwal_harian', 'id');
    }

    public function member(){
        return $this->belongsTo(member::class, 'id_member', 'id');
    }

    public function depositkelas(){
        return $this->belongsTo(DepositKelas::class, 'id_deposit_kelas', 'id');
    }
}
