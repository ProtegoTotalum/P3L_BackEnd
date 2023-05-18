<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IjinInstruktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_instruktur',
        'tanggal_pengajuan_ijin',
        'tanggal_ijin_instruktur',
        'hari_ijin',
        'sesi_ijin',
        'alasan_ijin',
        'id_instruktur_pengganti',
        'status_konfirmasi'
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

    public function instrukturpengganti()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur_pengganti', 'id');

    }
}
