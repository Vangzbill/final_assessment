<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_customer')->insert([
            'nama_perusahaan' => 'Surya Abadi Corp',
            'email_perusahaan' => 'mamadhan017@gmail.com',
            'no_telp_perusahaan' => '081234567890',
            'npwp_perusahaan' => '1234567890',
            'username' => 'surya_abadi',
            'password' => bcrypt('Password12@'),
            'alamat' => 'Jl. Raya Kaliwungu No. 123',
            'provinsi_id' => 'Jawa Tengah',
            'kota_id' => 'Kudus',
            'kecamatan_id' => 'Kaliwungu',
            'kelurahan_id' => 'Kaliwungu',
            'latitude' => '-6.8045',
            'longitude' => '110.8403',
            'email_verified_at' => now(),
        ]);
    }
}
