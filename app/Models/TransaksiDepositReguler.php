<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiDepositReguler extends Model
{
    use HasFactory;

    protected $primaryKey = 'nomor_struk_deposit_reguler';

    protected $fillable =[
        'nomor_struk_deposit_reguler',
        'id_pegawai',
        'id_member',
        'id_promo',
        'tanggal_deposit_reguler',
        'nominal_deposit_reguler',
        'bonus_deposit_reguler',
        'total_deposit_reguler',
    ];

    protected $casts = [
        'nomor_struk_deposit_reguler' => 'string'
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

    public function member(){
        return $this->belongsTo(member::class, 'id_member', 'id');
    }

    public function promo(){
        return $this->belongsTo(promo::class, 'id_promo', 'id');
    }

}
