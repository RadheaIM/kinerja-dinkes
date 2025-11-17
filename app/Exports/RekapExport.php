<?php

namespace App\Exports;

use App\Models\Laporan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahun;

    // Constructor menerima tahun, default ke tahun sekarang
    public function __construct($tahun = null)
    {
        $this->tahun = $tahun ?? date('Y');
    }

    /**
     * Ambil data laporan sesuai tahun
     */
    public function collection()
    {
        return Laporan::with('pegawai')
            ->whereYear('created_at', $this->tahun)
            ->get();
    }

    /**
     * Tentukan kolom yang akan muncul di Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Judul Laporan',
            'Deskripsi',
            'Tanggal Upload',
        ];
    }

    /**
     * Tentukan data tiap baris sesuai kolomnya
     */
    public function map($laporan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $laporan->pegawai->nama ?? '-',
            $laporan->judul ?? '-',
            $laporan->deskripsi ?? '-',
            $laporan->created_at->format('d-m-Y'),
        ];
    }
}
