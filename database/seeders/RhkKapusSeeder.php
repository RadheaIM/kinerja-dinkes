<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RhkKapus;

class RhkKapusSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $data = [
            [
                'rhk_kadis' => 'Meningkatkan Pelayanan Kesehatan Dasar',
                'rhk_kapus' => 'Peningkatan Kualitas Layanan Puskesmas',
                'indikator_kinerja' => 'Persentase Puskesmas dengan pelayanan sesuai standar',
                'aspek' => 'Pelayanan',
                'target_tahunan' => '90%',
                'rencana_aksi' => 'Melakukan supervisi dan peningkatan sarana prasarana Puskesmas'
            ],
            [
                'rhk_kadis' => 'Meningkatkan Derajat Kesehatan Masyarakat',
                'rhk_kapus' => 'Penguatan Program Kesehatan Masyarakat',
                'indikator_kinerja' => 'Persentase capaian program UKM',
                'aspek' => 'Program',
                'target_tahunan' => '85%',
                'rencana_aksi' => 'Pelaksanaan program UKM sesuai target'
            ],
        ];

        RhkKapus::insert($data);
    }
}
    