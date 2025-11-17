<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SasaranPuskesmasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'puskesmas' => 'Puskesmas Sukajadi',
                'bumil' => 120,
                'bulin' => 100,
                'bbl' => 95,
                'balita' => '400/420',
                'pendidikan_dasar' => 98,
                'uspro' => 85,
                'lansia' => 210,
                'hipertensi' => 60,
                'dm' => 40,
                'odgj_berat' => 15,
                'tb' => 10,
                'hiv' => 5,
                'idl' => 7,
            ],
            [
                'puskesmas' => 'Puskesmas Tarogong',
                'bumil' => 90,
                'bulin' => 80,
                'bbl' => 70,
                'balita' => '350/400',
                'pendidikan_dasar' => 95,
                'uspro' => 88,
                'lansia' => 190,
                'hipertensi' => 55,
                'dm' => 35,
                'odgj_berat' => 12,
                'tb' => 9,
                'hiv' => 4,
                'idl' => 6,
            ],
        ];

        DB::table('sasaran_puskesmas')->insert($data);
    }
}
