<?php

namespace App\Exports;

use App\Models\LaporanKinerja;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LaporanKinerjaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bulan;
    protected $tahun;

    // Konstruktor untuk menerima filter bulan & tahun
    public function __construct($bulan = null, $tahun = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * Ambil semua data dari tabel dengan filter bulan/tahun (jika ada)
     */
    public function collection()
    {
        $query = LaporanKinerja::query();

        if ($this->bulan && $this->tahun) {
            $query->whereMonth('created_at', $this->bulan)
                  ->whereYear('created_at', $this->tahun);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Format data per baris di file Excel
     */
    public function map($laporan): array
    {
        return [
            $laporan->kategori,
            $laporan->unit,
            $laporan->judul,
            $laporan->indikator,
            $laporan->target,
            $laporan->realisasi,
            $laporan->persentase . '%',
            $laporan->keterangan,
            optional($laporan->pegawai)->nama ?? '-',
            $laporan->created_at ? $laporan->created_at->format('d/m/Y') : '-',
        ];
    }

    /**
     * Judul kolom di Excel hasil export
     */
    public function headings(): array
    {
        return [
            'Kategori',
            'Unit',
            'Judul',
            'Indikator',
            'Target',
            'Realisasi',
            'Persentase',
            'Keterangan',
            'Nama Pegawai',
            'Tanggal Upload',
        ];
    }
}
