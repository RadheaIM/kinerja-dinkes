<?php

namespace App\Imports;

use App\Models\LaporanKinerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LaporanKinerjaImport implements ToModel, WithHeadingRow
{
    /**
     * Proses setiap baris Excel → model database
     */
    public function model(array $row)
    {
        return new LaporanKinerja([
            'kategori'   => $row['kategori'] ?? 'Tidak Diketahui',
            'unit'       => $row['unit'] ?? null,
            'judul'      => $row['judul'] ?? null,
            'indikator'  => $row['indikator'] ?? null,
            'target'     => $row['target'] ?? null,
            'realisasi'  => $row['realisasi'] ?? null,
            'persentase' => $row['persentase'] ?? null,
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }
}
