<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalUmum extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_instruktur',
        'id_kelas',
        'hari',
        'jam',
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

    public function kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
}
