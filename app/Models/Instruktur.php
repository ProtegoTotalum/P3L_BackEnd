<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Instruktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'nama_instruktur',
        'email_instruktur',
        'nomor_telepon_instruktur',
        'username_instruktur',
        'password_instruktur',
        'jumlah_keterlambatan_instruktur',
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

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
