<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER `increment_nomor_booking_kelas` BEFORE INSERT ON `booking_kelas` FOR EACH ROW 
            BEGIN 
            DECLARE last_id INT; 
            DECLARE new_id VARCHAR(255); 
            SET last_id = ( 
                SELECT MAX(RIGHT(nomor_booking_kelas,3))
                FROM booking_kelas ); 
            IF last_id IS NULL THEN 
                SET new_id = CONCAT(DATE_FORMAT(NOW(), '%y.%m.'), '001'); 
            ELSE 
                SET new_id = CONCAT(DATE_FORMAT(NOW(), '%y.%m.'), LPAD(last_id + 1, 3, '0')); 
            END IF; 
            SET NEW.nomor_booking_kelas = new_id; 
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `increment_nomor_booking_kelas`');
    }
};
