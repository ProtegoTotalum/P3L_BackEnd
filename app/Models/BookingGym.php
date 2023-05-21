<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookingGym extends Model
{
    use HasFactory;

    protected $primaryKey = 'nomor_booking_gym';

    protected $fillable =[
        'nomor_booking_gym',
        'id_member',
        'tanggal_booking_gym',
        'tanggal_pelaksanaan_gym',
        'jam_sesi_booking_gym',
        'kapasitas_gym',
        'jam_presensi_gym',
    ];

    protected $casts = [
        'nomor_booking_gym' => 'string'
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

    public function member(){
        return $this->belongsTo(member::class, 'id_member', 'id');
    }
}
