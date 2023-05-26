<?php

namespace Database\Seeders;

use App\Models\Instruktur;
use App\Models\JadwalUmum;
use App\Models\Promo;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Member;
use App\Models\Pegawai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Rani Remi',
            'email_pegawai' => 'emailp3lgray@gmail.com',
            'nama_jabatan_pegawai' => 'kasir',
            'nomor_telepon_pegawai' => '0123456789',
            'username_pegawai' => 'rani',
            'password_pegawai' => '123',
        ]);

        $user = new User();
        $user->id_user_login = $pegawai->id;
        $user->name = $pegawai->nama_pegawai;
        $user->email = $pegawai->email_pegawai;
        $user->username = $pegawai->username_pegawai;
        $user->password = $pegawai->password_pegawai;
        $user->role = $pegawai->nama_jabatan_pegawai;
        $user->password = bcrypt($pegawai->password_pegawai);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $pegawai->id_user = $user->id;
        $pegawai->save();
        
        $pegawai2 = Pegawai::create([
            'nama_pegawai' => 'Andi',
            'email_pegawai' => 'emailp3lgray2@gmail.com',
            'nama_jabatan_pegawai' => 'mo',
            'nomor_telepon_pegawai' => '987654321',
            'username_pegawai' => 'andi',
            'password_pegawai' => '123',
        ]);

        $user = new User();
        $user->id_user_login = $pegawai2->id;
        $user->name = $pegawai2->nama_pegawai;
        $user->email = $pegawai2->email_pegawai;
        $user->username = $pegawai2->username_pegawai;
        $user->password = $pegawai2->password_pegawai;
        $user->role = $pegawai2->nama_jabatan_pegawai;
        $user->password = bcrypt($pegawai2->password_pegawai);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $pegawai2->id_user = $user->id;
        $pegawai2->save();

        $pegawai3 = Pegawai::create([
            'nama_pegawai' => 'Grayfien Halim',
            'email_pegawai' => 'grayfien2002@gmail.com',
            'nama_jabatan_pegawai' => 'admin',
            'nomor_telepon_pegawai' => '0123456789',
            'username_pegawai' => 'admin',
            'password_pegawai' => '123',
        ]);

        $user = new User();
        $user->id_user_login = $pegawai3->id;
        $user->name = $pegawai3->nama_pegawai;
        $user->email = $pegawai3->email_pegawai;
        $user->username = $pegawai3->username_pegawai;
        $user->password = $pegawai3->password_pegawai;
        $user->role = $pegawai3->nama_jabatan_pegawai;
        $user->password = bcrypt($pegawai3->password_pegawai);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $pegawai3->id_user = $user->id;
        $pegawai3->save();

        //Instruktur
        $instruktur = Instruktur::create([
            'nama_instruktur' => 'Hobby',
            'email_instruktur' => 'emailp3lgray3@gmail.com',
            'nomor_telepon_instruktur' => '080012345678',
            'username_instruktur' => 'hobby',
            'password_instruktur' => 'hobby',
            'jumlah_keterlambatan_instruktur' => '0'
        ]);  

        $user = new User();
        $user->id_user_login = $instruktur->id;
        $user->name = $instruktur->nama_instruktur;
        $user->email = $instruktur->email_instruktur;
        $user->username = $instruktur->username_instruktur;
        $user->password = $instruktur->password_instruktur;
        $user->role = 'instruktur';
        $user->password = bcrypt($instruktur->password_instruktur);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $instruktur->id_user = $user->id;
        $instruktur->save();

        Instruktur::create([
            'nama_instruktur' => 'Jenni',
            'email_instruktur' => 'jenni@gmail.com',
            'nomor_telepon_instruktur' => '1234567890',
            'username_instruktur' => 'jenni',
            'password_instruktur' => 'jenni',
            'jumlah_keterlambatan_instruktur' => '0'
        ]);  

        Instruktur::create([
            'nama_instruktur' => 'Rose',
            'email_instruktur' => 'rose@gmail.com',
            'nomor_telepon_instruktur' => '0987654321',
            'username_instruktur' => 'rose',
            'password_instruktur' => 'rose',
            'jumlah_keterlambatan_instruktur' => '0'
        ]);  
        
        Kelas::create([
            'nama_kelas' => 'Pilates',
            'harga_kelas' => '150000',
            'kapasitas_kelas' => '10',
        ]);     

        Kelas::create([
            'nama_kelas' => 'Yoga',
            'harga_kelas' => '100000',
            'kapasitas_kelas' => '10',
        ]);    

        Kelas::create([
            'nama_kelas' => 'Wall Swing',
            'harga_kelas' => '150000',
            'kapasitas_kelas' => '10',
        ]);    
        
        Promo::create([
            'jenis_promo' => 'Reguler',
            'deskripsi_promo' => 'Setiap deposit Rp.3.000.000,- mendapat bonus deposit Rp.300.000,-. Uang yang sudah didepositkan tidak dapat diminta kembali.',
            'minimal_deposit' => '3000000',
            'bonus_deposit' => '300000',
        ]); 

        Promo::create([
            'jenis_promo' => 'Paket 5 Kelas',
            'deskripsi_promo' => 'Bayar 5 kelas, gratis 1 kelas. Berlaku 1 bulan sejak pembayaran.',
            'minimal_deposit' => '5',
            'bonus_deposit' => '1',
        ]);

        Promo::create([
            'jenis_promo' => 'Paket 10 Kelas',
            'deskripsi_promo' => 'Bayar 10 kelas, gratis 3 kelas. Berlaku 2 bulan sejak pembayaran.',
            'minimal_deposit' => '10',
            'bonus_deposit' => '3',
        ]);

        $member = Member::create([
            'nama_member' => 'Jett',
            'email_member' => 'halohalo2721@gmail.com',
            'nomor_telepon_member' => '1234567890',
            'tanggal_lahir_member' => '25-07-1997',
            'alamat_member' => 'Jl. Babarsari',
            'sisa_deposit_reguler' => '0',
            'status_member' => 'Tidak Aktif',
            'username_member' => 'jett',
            'password_member' => 'jett',
        ]);  

        $user = new User();
        $user->id_user_login = $member->id;
        $user->name = $member->nama_member;
        $user->email = $member->email_member;
        $user->username = $member->username_member;
        $user->password = $member->password_member;
        $user->role = 'member';
        $user->password = bcrypt($member->password_member);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $member->id_user = $user->id;
        $member->save();

        $member2 = Member::create([
            'nama_member' => 'Sage',
            'email_member' => 'sage@gmail.com',
            'nomor_telepon_member' => '0987654321',
            'tanggal_lahir_member' => '09-03-1987',
            'alamat_member' => 'Jl. Soekarno Hatta',
            'sisa_deposit_reguler' => '0',
            'status_member' => 'Tidak Aktif',
            'username_member' => 'sage',
            'password_member' => 'sage',
        ]);  

        $user = new User();
        $user->id_user_login = $member2->id;
        $user->name = $member2->nama_member;
        $user->email = $member2->email_member;
        $user->username = $member2->username_member;
        $user->password = $member2->password_member;
        $user->role = 'member';
        $user->password = bcrypt($member2->password_member);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $member2->id_user = $user->id;
        $member2->save();

        $member3 = Member::create([
            'nama_member' => 'Viper',
            'email_member' => 'viper@gmail.com',
            'nomor_telepon_member' => '123123123',
            'tanggal_lahir_member' => '17-11-1985',
            'alamat_member' => 'Jl. Ahmad Yani',
            'sisa_deposit_reguler' => '0',
            'status_member' => 'Tidak Aktif',
            'username_member' => 'viper',
            'password_member' => 'viper',
        ]);  

        $user = new User();
        $user->id_user_login = $member3->id;
        $user->name = $member3->nama_member;
        $user->email = $member3->email_member;
        $user->username = $member3->username_member;
        $user->password = $member3->password_member;
        $user->role = 'member';
        $user->password = bcrypt($member3->password_member);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $member3->id_user = $user->id;
        $member3->save();

        JadwalUmum::create([
            'id_instruktur' => '1',
            'id_kelas' => '3',
            'hari' => 'Senin',
            'jam' => '09:00',
            ],
        );
        JadwalUmum::create([
            'id_instruktur' => '1',
            'id_kelas' => '3',
            'hari' => 'Minggu',
            'jam' => '15:00',
            ],
        );
        JadwalUmum::create([
            'id_instruktur' => '2',
            'id_kelas' => '1',
            'hari' => 'Rabu',
            'jam' => '17:00',
            ],
        );
        JadwalUmum::create([
            'id_instruktur' => '2',
            'id_kelas' => '1',
            'hari' => 'Selasa',
            'jam' => '10:00',
            ],
        );
        JadwalUmum::create([
            'id_instruktur' => '3',
            'id_kelas' => '2',
            'hari' => 'Jumat',
            'jam' => '12:00',
            ],
        );
        JadwalUmum::create([
            'id_instruktur' => '3',
            'id_kelas' => '2',
            'hari' => 'Sabtu',
            'jam' => '08:00',
            ],
        );
        JadwalUmum::create([
            'id_instruktur' => '3',
            'id_kelas' => '2',
            'hari' => 'Sabtu',
            'jam' => '14:00',
            ],
        );
    }
}
