<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PresensiInstruktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_instruktur',
        'id_jadwal_harian',
        'jam_mulai_kelas',
        'jam_selesai_kelas',
        'status_presensi',
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

    public function instruktur(){
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id');
    }

    public function jadwalharian(){
        return $this->belongsTo(JadwalHarian::class, 'id_jadwal_harian', 'id');
    }
}
