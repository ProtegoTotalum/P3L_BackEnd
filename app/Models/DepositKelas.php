<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DepositKelas extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_member',
        'id_kelas',
        'sisa_deposit_kelas',
        'masa_berlaku_deposit_kelas',
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
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }

    public function kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
}
