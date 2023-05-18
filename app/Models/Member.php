<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_member',
        'id_user',
        'nama_member',
        'email_member',
        'nomor_telepon_member',
        'tanggal_lahir_member',
        'alamat_member',
        'sisa_deposit_reguler',
        'masa_berlaku_member',
        'status_member',
        'username_member',
        'password_member'
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
