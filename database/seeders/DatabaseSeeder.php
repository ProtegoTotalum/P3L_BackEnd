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
        User::create([
            'name' => 'Rani',
            'email' => 'emailp3lgray@gmail.com',
            'email_verified_at' => now(),
            'username' => 'Rani',
            'password' => bcrypt('123'),
            'role' => 'kasir',
            ],
        );

        User::create([
            'name' => 'Andi',
            'email' => 'emailp3lgray2@gmail.com',
            'email_verified_at' => now(),
            'username' => 'andi',
            'password' => bcrypt('123'),
            'role' => 'mo',
            ],
        );

        User::create([
            'name' => 'Gray',
            'email' => 'grayfien2002@gmail.com',
            'email_verified_at' => now(),
            'username' => 'gray',
            'password' => bcrypt('123'),
            'role' => 'admin',
            ],
        );

        Pegawai::create([
            'id_user' => '1',
            'nama_pegawai' => 'Rani Remi',
            'nama_jabatan_pegawai' => 'Kasir',
            'nomor_telepon_pegawai' => '0123456789',
        ]);
        
        Pegawai::create([
            'id_user' => '2',
            'nama_pegawai' => 'Andi Gunawan',
            'nama_jabatan_pegawai' => 'Manager Operasionam',
            'nomor_telepon_pegawai' => '987654321',
        ]); 

        Pegawai::create([
            'id_user' => '3',
            'nama_pegawai' => 'Grey',
            'nama_jabatan_pegawai' => 'Admin',
            'nomor_telepon_pegawai' => '08123456789',
        ]); 

        //Instruktur
        $instruktur = Instruktur::create([
            'nama_instruktur' => 'Hobby',
            'email_instruktur' => 'emailp3lgray3@gmail.com',
            'nomor_telepon_instruktur' => '080012345678',
            'username_instruktur' => 'hobby',
            'password_instruktur' => 'hobby',
            'jumlah_keterlambatan_instruktur' => '2'
        ]);  

        $user = new User();
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
            'jumlah_keterlambatan_instruktur' => '1'
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

        Member::create([
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

        Member::create([
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
