<?php
// File: app/Http/Controllers/RekapController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKinerja;
use App\Models\KinerjaCapaianDetail;
use App\Models\AdministrasiTu; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Collection; 
use Barryvdh\DomPDF\Facade\Pdf; // Import facade PDF

class RekapController extends Controller
{
    /**
     * Menampilkan rekapitulasi gabungan Capaian Program & Administrasi.
     */
    public function index(Request $request)
    {
        // === 1. PERSIAPAN FILTER ===
        $bulan = $request->input('bulan'); 
        $tahun = $request->input('tahun', date('Y'));
        $laporanIdGrafik = $request->input('laporan_id');

        // Ambil daftar tahun unik
        $yearsKinerja = LaporanKinerja::distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $yearsAdminTu = AdministrasiTu::distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $availableYears = $yearsKinerja->merge($yearsAdminTu)->unique()->sortDesc();
        $currentYear = date('Y');
        if ($availableYears->isEmpty() || !$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }
        $bulanNames = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // === 2. AMBIL DATA KINERJA PUSKESMAS & PAGINASI ===
        $queryPuskesmas = LaporanKinerja::with('details')
                            ->where('jenis_laporan', 'capaian_program')
                            ->where('puskesmas_name', '!=', 'Labkesda') 
                            ->where('tahun', $tahun)
                            ->orderBy('puskesmas_name');

        // Data yang dipaginasi (UNTUK TAB 1: Detail Laporan)
        $laporansPuskesmas = (clone $queryPuskesmas)->paginate(10, ['*'], 'puskesmas_page');

        // Data Ringkasan (UNTUK TAB 2: Ringkasan Capaian)
        $rekapKinerjaPuskesmas = collect();
        foreach ((clone $queryPuskesmas)->get() as $laporan) {
            $totalRealisasi = 0;
            $totalTarget = 0;
            $bln_col = $bulan ? 'bln_' . (int)$bulan : null;

            foreach ($laporan->details as $detail) {
                $totalTarget += $detail->target_sasaran ?? 0;
                if ($bln_col) {
                    $totalRealisasi += $detail->$bln_col ?? 0;
                } else {
                    for ($i = 1; $i <= 12; $i++) {
                        $totalRealisasi += $detail['bln_'.$i] ?? 0;
                    }
                }
            }
            $persentase = ($totalTarget > 0) ? round(($totalRealisasi / $totalTarget) * 100, 2) : 0;
            $rekapKinerjaPuskesmas->push([
                'id' => $laporan->id,
                'puskesmas_name' => $laporan->puskesmas_name,
                'tahun' => $laporan->tahun,
                'jenis_laporan' => 'Capaian Program',
                'total_target' => $totalTarget,
                'total_realisasi' => $totalRealisasi,
                'persentase' => $persentase,
            ]);
        }


        // === 3. AMBIL DATA REKAP KINERJA LABKESDA ===
        $queryLabkesda = LaporanKinerja::with('details')
                            ->where('jenis_laporan', 'labkesda_capaian')
                            ->where('puskesmas_name', 'Labkesda')
                            ->where('tahun', $tahun);

        $laporansLabkesda = $queryLabkesda->first(); 
        $rekapKinerjaLabkesda = null;

        if($laporansLabkesda){
            $totalRealisasiLab = 0;
            $bln_col_lab = $bulan ? 'bln_' . (int)$bulan : null;
             foreach ($laporansLabkesda->details as $detail) {
                 if ($bln_col_lab) {
                    $totalRealisasiLab += $detail->$bln_col_lab ?? 0;
                } else {
                    for ($i = 1; $i <= 12; $i++) {
                        $totalRealisasiLab += $detail['bln_'.$i] ?? 0;
                    }
                }
             }
             $rekapKinerjaLabkesda = [
                'id' => $laporansLabkesda->id,
                'puskesmas_name' => $laporansLabkesda->puskesmas_name,
                'tahun' => $laporansLabkesda->tahun,
                'jenis_laporan' => 'Capaian Labkesda',
                'total_target' => null,
                'total_realisasi' => $totalRealisasiLab,
                'persentase' => null,
            ];
        }


        // === 4. GABUNGKAN DATA RINGKASAN (UNTUK TABEL UTAMA) ===
        // Note: Meskipun bernama 'rekapData', ini adalah Collection biasa, bukan Paginator.
        $rekapData = $rekapKinerjaPuskesmas; 
        if ($rekapKinerjaLabkesda) {
            $rekapData = $rekapData->push($rekapKinerjaLabkesda);
        }


        // === 5. AMBIL DATA REKAP ADMINISTRASI & TU (DIPISAH) ===
        $rekapAdminTuPuskesmas = AdministrasiTu::select('puskesmas_name', 'tahun', 'jenis_laporan')
                                             ->where('tahun', $tahun)
                                             ->where('jenis_laporan', 'puskesmas') 
                                             ->where('puskesmas_name', '!=', 'Labkesda')
                                             ->distinct()
                                             ->orderBy('puskesmas_name')
                                             ->get();

        $rekapAdminTuLabkesda = AdministrasiTu::select('puskesmas_name', 'tahun', 'jenis_laporan')
                                             ->where('tahun', $tahun)
                                             ->where('puskesmas_name', 'Labkesda')
                                             ->distinct() 
                                             ->first(); 


        // === 6. PERSIAPAN DATA GRAFIK (Hanya untuk Puskesmas) ===
        $chartData = null; $chartTitle = null; $chartYear = null; $selectedLaporanGrafik = null;

        if ($laporanIdGrafik) {
            $selectedLaporanGrafik = LaporanKinerja::with('details')
                                                 ->where('jenis_laporan', 'capaian_program')
                                                 ->where('puskesmas_name', '!=', 'Labkesda')
                                                 ->find($laporanIdGrafik);
        } elseif ($laporansPuskesmas->isNotEmpty()) {
            $selectedLaporanGrafik = LaporanKinerja::with('details')->find($laporansPuskesmas->first()->id);
        }

        if ($selectedLaporanGrafik) {
            $chartTitle = $selectedLaporanGrafik->puskesmas_name;
            $chartYear = $selectedLaporanGrafik->tahun;
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartDatasets = [];
            $colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#6366f1', '#ec4899', '#8b5cf6', '#14b8a6', '#f97316', '#d946ef', '#06b6d4', '#84cc16', '#e11d48'];

            foreach($selectedLaporanGrafik->details as $index => $detail) {
                $monthlyData = [];
                for ($i = 1; $i <= 12; $i++) { $monthlyData[] = $detail['bln_'.$i] ?? 0; }
                $chartDatasets[] = [
                    'label' => $detail->indikator_name, 'data' => $monthlyData,
                    'borderColor' => $colors[$index % count($colors)] ?? '#cccccc',
                    'backgroundColor' => ($colors[$index % count($colors)] ?? '#cccccc') . '33',
                    'fill' => false, 'tension' => 0.1
                ];
            }
            $chartData = ['labels' => $labels, 'datasets' => $chartDatasets];
        }

        // === 7. PERSIAPAN LIST INDIKATOR UNTUK DROPDOWN FILTER GRAFIK ===
        $listIndikator = collect();
        if ($chartData && isset($chartData['datasets'])) {
            // Ambil label (nama indikator) dari setiap dataset
            $listIndikator = collect($chartData['datasets'])->pluck('label');
        }
        // ____________________________________________________________________

        // === 8. KIRIM SEMUA DATA KE VIEW ===
        return view('rekap.index', compact(
            'rekapData', // Collection
            'rekapKinerjaPuskesmas', // Collection
            'rekapKinerjaLabkesda', 
            'rekapAdminTuPuskesmas', 
            'rekapAdminTuLabkesda', 
            'laporansPuskesmas', // Paginator (Correct data source for Tab 1)
            'laporansLabkesda', 
            'chartData', 'chartTitle', 'chartYear', 'selectedLaporanGrafik', 
            'bulan', 'tahun', 'availableYears', 'bulanNames', 
            'listIndikator' // <--- VARIABEL BARU
        ));
    }

    /**
     * Menampilkan detail spesifik dari Laporan Kinerja.
     * @param LaporanKinerja $laporan
     * @return \Illuminate\View\View
     */
    public function show(LaporanKinerja $laporan)
    {
        // Pastikan details (capaian) ikut dimuat
        $laporan->load('details');
        
        // Definisikan nama bulan untuk header tabel
        $bulanNames = [
             1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
             5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
             9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('rekap.show', compact('laporan', 'bulanNames'));
    }

    /**
     * Menghasilkan dan mendownload laporan dalam format PDF.
     * Catatan: Membutuhkan package PDF seperti Barryvdh/Laravel-Dompdf
     * @param LaporanKinerja $laporan
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(LaporanKinerja $laporan)
    {
        try {
            // Pastikan details (capaian) ikut dimuat
            $laporan->load('details');
            
            // Definisikan nama bulan
            $bulanNames = [
                 1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            // Render view PDF
            $pdf = Pdf::loadView('rekap.pdf', compact('laporan', 'bulanNames'));
            
            // Tentukan nama file
            $fileName = 'Laporan Kinerja - ' . $laporan->puskesmas_name . ' (' . $laporan->tahun . ').pdf';
            
            // Download PDF
            return $pdf->download($fileName);
            
        } catch (\Exception $e) {
            // Tangani error jika package PDF belum diinstal atau terjadi masalah render
            \Log::error('PDF Download Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghasilkan PDF. Pastikan package Dompdf sudah terinstal dan view "rekap/pdf.blade.php" tersedia.');
        }
    }

    // Export function perlu penyesuaian
    public function export(Request $request)
    {
        return redirect()->back()->with('error', 'Fitur Export perlu diimplementasikan.');
    }
}