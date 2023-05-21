<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalHarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_instruktur',
        'id_jadwal_umum',
        'tanggal_jadwal_harian',
        'status_jadwal_harian',
        'kapasitas_kelas'
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

    public function instruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id');

    }
    public function jadwalumum()
    {
        return $this->belongsTo(JadwalUmum::class, 'id_jadwal_umum', 'id');

    }

    public function presensiInstrukturs()
    {
        return $this->hasMany(PresensiInstruktur::class, 'id_jadwal_harian');
    }
}
