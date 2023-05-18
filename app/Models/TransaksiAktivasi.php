<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiAktivasi extends Model
{
    use HasFactory;

    protected $primaryKey = 'nomor_struk_transaksi_aktivasi';

    protected $fillable =[
        'nomor_struk_transaksi_aktivasi',
        'id_pegawai',
        'id_member',
        'tanggal_transaksi_aktivasi',
        'nominal_transaksi_aktivasi',
    ];

    protected $casts = [
        'nomor_struk_transaksi_aktivasi' => 'string'
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
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }
}
