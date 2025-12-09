<?php

namespace App\Http\Controllers;

use App\Models\LaporanKinerja;
use App\Models\KinerjaCapaianDetail;
use App\Models\SasaranPuskesmas;
use App\Models\TargetSasaran; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class LaporanKinerjaController extends Controller
{
    // === INDIKATOR PUSKESMAS (19 INDIKATOR) ===
    private $puskesmasIndicators = [
        'Pelayanan Kesehatan Ibu Hamil',
        'Pelayanan Kesehatan Ibu Bersalin',
        'Pelayanan Kesehatan Bayi Baru Lahir',
        'Pelayanan Kesehatan Balita',
        'Pelayanan Kesehatan pada Usia Pendidikan Dasar',
        'Pelayanan Kesehatan pada Usia Produktif',
        'Pelayanan Kesehatan pada Usia Lanjut',
        'Pelayanan Kesehatan Penderita Hipertensi',
        'Pelayanan Kesehatan Penderita Diabetes Melitus',
        'Pelayanan Kesehatan Orang Dengan Gangguan Jiwa (ODGJ) Berat',
        'Pelayanan Kesehatan Orang Terduga Tuberkulosis',
        'Pelayanan Kesehatan Orang Dengan Risiko Terinfeksi Virus HIV',
        'Capaian D/S',
        'Capaian IDL',
        'Capaian IKS', 
        'Cek Kesehatan Gratis',
        'Jumlah Balita Stunting',
        'Kematian Ibu',
        'Kematian Bayi',
        'Jumlah Kunjungan Rawat Jalan',
        'Jumlah Kunjungan Rawat Inap',
        'Kejadian KLB',
    ];

    // === INDIKATOR LABKESDA (LENGKAP) ===
    private $labkesdaIndicators = [
        'Pelayanan',
        'a. Pertumbuhan Produktivitas',
        '1. Pertumbuhan Rata-rata Pemeriksaan Mikrobiologi',
        '   - Rata- rata pemeriksaan mikrobiologi per hari pada bulan berjalan',
        '   - Rata- ratapemeriksaan mikrobiologi per hari bulan lalu',
        '2. Pertumbuhan Rata-rata Pemeriksaan Imunologi',
        '   - Rata- rata Rata-rata Pemeriksaan Imunologi per hari pada bulan berjalan',
        '   - Rata- rata Rata-rata Pemeriksaan Imunologi per hari bulan lalu',
        '3. Pertumbuhan Rata-rata Pemeriksaan Patologi Klinik',
        '   - Rata-rata pemeriksaan patologi klinik per hari pada bulan berjalan',
        '   - Rata-rata pemeriksaan patologi klinik per hari pada bulan lalu',
        '4. Pertumbuhan Rata-rata Pemeriksaan Kimia Kesehatan',
        '   - Rata- rata pemeriksaan pemeriksaan kimia per hari bulan berjalan',
        '   - Rata- rata pemeriksaan kimia per hari bulan lalu',
        '5. Pertumbuhan Rata-rata Pembuatan Media dan Reagensia',
        '   - Rata- rata pembuatan media dan reagensia per hari bulan berjalan',
        '   - Rata- rata pembuatan media dan reagensia per hari bulan lalu',
        '6. Pertumbuhan Rata-rata Pemeriksaan Uji Kesehatan',
        '   - Rata- rata pemeriksaan uji kesehatan per hari bulan berjalan',
        '   - Rata- rata pemeriksaan uji kesehatan per hari bulan lalu',
        'b. Efisiensi Pelayanan',
        '1. Rasio Jumlah Pemeriksaan Mikrobiologi dengan Analis',
        '   - Rata - rata jumlah parameter pemeriksaaan mikrobiologi per hari',
        '   - Jumlah analis yang melakukan pemeriksaan per hari',
        '2. Rasio Jumlah Pemeriksaan Imunologi dengan Analis',
        '   - Rata- rata jumlah pemeriksaaan imunologi per hari',
        '   - jumlah analis yang melakukan pemeriksaan per hari',
        '3. Rasio Jumlah Pemeriksaaan Patologi Klinik dengan Analis',
        '   - Rata- rata jumlah parameter pemeriksaan patologi per hari',
        '   - Jumlah analisyang melakukan pemeriksaan per hari',
        '4. Rasio Jumlah Pemeriksaan Kimia Kesehatan dengan Analis',
        '   - Rata- rata jumlah pemeriksaan kimia kesehatan per hari',
        '   - Jumlah analis yang melakukan pemeriksaan per hari',
        '5. Rasio Jumlah Pembuatan Media Reagensia dengan Analis',
        '   - Rata- rata jumlah pembuatan media reagensia per hari',
        '   - Jumlah analis yang melakukan pemeriksaan per hari',
        '6. Rasio Jumlah Pemeriksaan Laboratorium Klinik dengan Dokter Spesialis Patologi Klinik',
        '   - Rata- rata jumlah pemeriksaan laboratorium klinik per hari',
        '   - jumlah dokter Sp.PK yang melakukan tugas fungsional laboratorium klinik per hari',
        '7. Rasio Jumlah Pemeriksaan Uji Kesehatan dengan Tenaga yang Bertugas di Unit Instalasi Uji Kesehatan',
        '   - Ratarata jumlah pemeriksaan uji kesehatan per hari',
        '   - Jumlah tenaga yang melaksanakan pemeriksaan uji kesehatan per hari',
        '8. Angka Pengulangan Pemeriksaan Laboratorium',
        '   - Rata- rata jumlah pemeriksaan laboratorium yang diulang per hari',
        '   - Jumlah seluruh pemeriksaan laboratorium per hari',
        'c. Pertumbuhan Pembelajaran',
        '1. Rata- rata Jam Pelatihan/ Karyawan',
        '   - Jumlah jam pelatihan pegawai dalam setahun',
        '   - Jumlah pegawai dalam tahun yang sama',
        '2. Penelitian',
        '3. Program Reward and Punishment',
        'Mutu dan Manfaat Kepada Masyarakat',
        'a. Mutu Pelayanan',
        '1. Waktu Tunggu Pelayanan',
        '   - Rata-rata lama waktu tunggu pasien sampai mendapatkan pelayanan di instalasi sampling (dalam menit)',
        '2. Waktu Layanan Bidang Pemeriksaan Mikrobiologi',
        '   - Rata-rata lama waktu penyelesaian pelayanan pemeriksaan mikrobiologi (dalam hari)',
        '3. Waktu Layanan Bidang Pemeriksaan Patologi Klinik',
        '   - Rata-rata lama waktu penyelesaian pelayanan pemeriksaan patologi klinik (dalam menit).',
        '4. Waktu Layanan Bidang Pemeriksaan Imunologi',
        '   - Rata-rata waktu penyelesaian pelayanan pemeriksaan imunologi (dalam menit).',
        '5. Waktu Layanan Bidang Pemeriksaan Kimia Kesehatan',
        '   - Rata-rata waktu penyelesaian pelayanan pemeriksaan kimia kesehatan (dalam hari)',
        '6. Waktu Layanan Bidang Pembuatan Media dan Reagensia',
        '   - Rata-rata waktu penyelesaian pelayanan pembuatan Media dan Reagensia (dalam hari).',
        '7. Waktu Layanan Bidang Pemeriksaan Uji Kesehatan',
        '   - Rata-rata waktu penyelesaian pelayanan uji kesehatan (dalam hari).',
        'b. Mutu Klinik',
        '1. Angka Kegagalan Pengambilan Sampel Uji',
        '   - Rata- rata kegagalan pengambilan sampel uji per hari',
        '   - Rata -rata pengambilan sampel uji per hari',
        '2. Angka Pemeriksaan Laboratorium yang Dirujuk',
        '   - Rata- rata jumlah sampel yang dirujuk per hari',
        '   - Rata- rata jumlah sampel per hari',
        '3. Hasil Kegiatan Pemantapan Mutu Internal',
        '   - Parameter pemeriksaan yang dilakukan pemantapan mutu internal',
        '   - Kemampuan pemeriksaan per parameter',
        '4. Hasil Pemantapan Mutu Ekternal',
        '   - Jumlah Parameter dengan hasil baik',
        '   - Jumlah parameter yang diikuti',
        'c. Kepedulian kepada Masyarakat',
        '1. Pembinaan Kepada Laboratorium Pusat Kesehatan Masyarakat dan sarana kesehatan lain',
        '2. Kegiatan Pelayanan PME Regional',
        '3. Program Penyuluhan Kesehatan',
        'd. Kepuasan Pelanggan',
        '1. Penanganan Pengaduan/Komplain',
        '   - Pengaduan Komplain tertulis yang telah ditindaklanjuti manajemen',
        '   - Jumlah seluruh pengaduan komplain tertulis yang dilaporkan',
        '2. Kepuasan Pelanggan',
        '   - Hasil penilaian IKM',
        '   - Skala Maksimal nilai IKM',
        'e. Kepedulian Terhadap Lingkungan',
        '1. Pengelolaan Limbah Bahan Berbahaya dan Beracun (B3)',
        '2. Proper Lingkungan',
    ];

    private function getIndicators($jenisLaporan)
    {
        return ($jenisLaporan === 'labkesda_capaian') ? $this->labkesdaIndicators : $this->puskesmasIndicators;
    }

    /**
     * ==================================================================
     * === HELPER PENCARIAN TARGET ===
     * ==================================================================
     */
    private function getRefreshedTargets($namaUnit, $tahun)
    {
        if (!$namaUnit) return [];

        // 1. Deteksi Kolom Target
        $colTarget = 'target_tahunan'; 
        if (Schema::hasColumn('target_sasarans', 'target_tahunan')) $colTarget = 'target_tahunan';
        elseif (Schema::hasColumn('target_sasarans', 'target_sasaran')) $colTarget = 'target_sasaran';
        elseif (Schema::hasColumn('target_sasarans', 'target_value')) $colTarget = 'target_value';
        elseif (Schema::hasColumn('target_sasarans', 'target')) $colTarget = 'target';

        // 2. Deteksi Kolom Nama Indikator
        $colName = 'indikator_name'; 
        if (!Schema::hasColumn('target_sasarans', 'indikator_name')) {
             if (Schema::hasColumn('target_sasarans', 'indikator_key')) $colName = 'indikator_key';
        }

        // 3. Cari Data Target
        $targets = [];
        $hasPuskesmasId = Schema::hasColumn('target_sasarans', 'puskesmas_id');
        $hasPuskesmasName = Schema::hasColumn('target_sasarans', 'puskesmas_name');

        // A. Coba Cari Pakai ID
        if ($hasPuskesmasId) {
            $puskesmasData = SasaranPuskesmas::where('puskesmas', $namaUnit)->first();
            if (!$puskesmasData) {
                $cleanName = str_replace(['puskesmas', 'Puskesmas', ' '], '', $namaUnit); 
                $puskesmasData = SasaranPuskesmas::where('puskesmas', 'LIKE', "%$cleanName%")->first();
            }

            if ($puskesmasData) {
                $targets = TargetSasaran::where('puskesmas_id', $puskesmasData->id)
                            ->where('tahun', $tahun)
                            ->pluck($colTarget, $colName) 
                            ->toArray();
            }
        }

        // B. Fallback: Cari Pakai Nama
        if (empty($targets) && $hasPuskesmasName) {
            $targets = TargetSasaran::where('puskesmas_name', $namaUnit)
                        ->where('tahun', $tahun)
                        ->pluck($colTarget, $colName) 
                        ->toArray();
            
            if (empty($targets)) {
                $cleanName = str_replace(['puskesmas', 'Puskesmas', ' '], '', $namaUnit);
                $targets = TargetSasaran::where('puskesmas_name', 'LIKE', "%$cleanName%")
                            ->where('tahun', $tahun)
                            ->pluck($colTarget, $colName) 
                            ->toArray();
            }
        }

        return $targets;
    }

    /**
     * ==================================================================
     * === FUNGSI INDEX (ADMIN) ===
     * ==================================================================
     */
    public function adminIndex(Request $request)
    {
         // 1. Ambil Laporan untuk Tabel
         $query = LaporanKinerja::where('jenis_laporan', 'capaian_program');
         $laporanIdGrafik = $request->input('laporan_id');
         $laporans = $query->orderBy('puskesmas_name')->paginate(10); 
 
         // 2. Logika untuk Chart
         $chartData = null;
         $chartTitle = null;
         $chartYear = null;
         $selectedLaporan = null;
 
         if ($laporanIdGrafik) {
             $selectedLaporan = LaporanKinerja::with('details')->find($laporanIdGrafik);
         } elseif ($laporans->isNotEmpty()) {
             $selectedLaporan = LaporanKinerja::with('details')->find($laporans->first()->id);
         }
 
         if ($selectedLaporan) {
             $chartTitle = $selectedLaporan->puskesmas_name;
             $chartYear = $selectedLaporan->tahun;
             $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
             $colors = ['#3b82f6', '#ef4444', '#22c55e', '#eab308', '#8b5cf6', '#ec4899', '#f97316', '#14b8a6', '#64748b', '#06b6d4', '#d946ef', '#f43f5e', '#84cc16', '#10b981', '#0ea5e9', '#6366f1', '#a855f7'];
             
             $chartDatasets = [];
             foreach ($selectedLaporan->details as $index => $detail) {
                 $monthlyData = [];
                 for ($i = 1; $i <= 12; $i++) { $monthlyData[] = $detail['bln_'.$i] ?? 0; }
                 $color = $colors[$index % count($colors)];
                 $chartDatasets[] = [
                     'label' => $detail->indikator_name, 'data' => $monthlyData,
                     'borderColor' => $color, 'backgroundColor' => $color . '33', 'fill' => false, 'tension' => 0.1
                 ];
             }
             $chartData = ['labels' => $labels, 'datasets' => $chartDatasets];
         }
 
         return view('laporan_kinerja.admin_index', compact('laporans', 'chartData', 'chartTitle', 'chartYear', 'selectedLaporan'));
    }

    /**
     * ==================================================================
     * === FUNGSI INDEX (USER/PUSKESMAS) ===
     * ==================================================================
     * Ini yang menyebabkan error "parentUserIndex". Sekarang sudah diperbaiki.
     */
    public function userIndex(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));
        
        $query = LaporanKinerja::where('tahun', $tahun);
        $yearsQuery = LaporanKinerja::distinct()->orderBy('tahun', 'desc');

        // Filter berdasarkan Role
        if ($user->role === 'labkesda') {
            $query->where('jenis_laporan', 'labkesda_capaian') 
                  ->where('puskesmas_name', 'Labkesda');
            $yearsQuery->where('jenis_laporan', 'labkesda_capaian')
                       ->where('puskesmas_name', 'Labkesda');
        } else { 
            // Jika role puskesmas, hanya tampilkan milik puskesmas tersebut
            $query->where('jenis_laporan', 'capaian_program')
                  ->where('puskesmas_name', $user->nama_puskesmas); 
            $yearsQuery->where('jenis_laporan', 'capaian_program')
                       ->where('puskesmas_name', $user->nama_puskesmas); 
        }
        
        $laporans = $query->paginate(20);
        
        // Siapkan dropdown tahun
        $availableYears = $yearsQuery->pluck('tahun');
        $currentYear = date('Y');
        
        if ($availableYears->isEmpty() || !$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }

        return view('laporan_kinerja.user_index', compact('laporans', 'availableYears', 'tahun'));
    }

    /**
     * ==================================================================
     * === FUNGSI CREATE ===
     * ==================================================================
     */
    public function create(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        
        $namaUnit = null;
        if (Auth::user()->role === 'puskesmas') {
            $namaUnit = Auth::user()->nama_puskesmas ?? Auth::user()->name; 
        } else {
            $namaUnit = $request->query('puskesmas');
        }

        // PANGGIL FUNGSI HELPER
        $savedTargets = $this->getRefreshedTargets($namaUnit, $tahun);

        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        $indicators = $this->getIndicators('capaian_program'); 
        $jenisLaporan = 'capaian_program';

        return view('laporan_kinerja.create', compact('indicators', 'puskesmasNames', 'jenisLaporan', 'savedTargets', 'tahun', 'namaUnit'));
    }

    public function createLabkesdaForm(Request $request)
    {
        $indicators = $this->getIndicators('labkesda_capaian');
        $labkesdaName = "Labkesda";
        $jenisLaporan = 'labkesda_capaian';
        return view('laporan_kinerja.labkesda_create', compact('indicators', 'labkesdaName', 'jenisLaporan'));
    }

    /**
     * ==================================================================
     * === FUNGSI STORE ===
     * ==================================================================
     */
    public function store(Request $request)
    {
        // Validasi
        $rules = [
            'puskesmas_name' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2020|max:2099',
            'keterangan' => 'nullable|string',
            'details' => "required|array",
            'details.*.bln_1' => 'nullable|numeric|min:0', 'details.*.bln_2' => 'nullable|numeric|min:0',
            'details.*.bln_3' => 'nullable|numeric|min:0', 'details.*.bln_4' => 'nullable|numeric|min:0',
            'details.*.bln_5' => 'nullable|numeric|min:0', 'details.*.bln_6' => 'nullable|numeric|min:0',
            'details.*.bln_7' => 'nullable|numeric|min:0', 'details.*.bln_8' => 'nullable|numeric|min:0',
            'details.*.bln_9' => 'nullable|numeric|min:0', 'details.*.bln_10' => 'nullable|numeric|min:0',
            'details.*.bln_11' => 'nullable|numeric|min:0', 'details.*.bln_12' => 'nullable|numeric|min:0',
            'jenis_laporan' => 'required|string', 
        ];
        
        $validated = $request->validate($rules);
        $user = Auth::user();
    
        DB::beginTransaction();
        try {
            $puskesmasName = '';
            $jenisLaporan = '';
            
            if ($user->role === 'admin') {
                $puskesmasName = $validated['puskesmas_name'];
                $jenisLaporan = $validated['jenis_laporan']; 
            } else {
                if (empty($user->nama_puskesmas)) {
                    throw new \Exception("Profil Anda tidak memiliki 'nama_puskesmas'.");
                }
                if ($user->role === 'labkesda') {
                    $puskesmasName = 'Labkesda';
                    $jenisLaporan = 'labkesda_capaian'; 
                } else {
                    $puskesmasName = $user->nama_puskesmas; 
                    $jenisLaporan = 'capaian_program'; 
                }
                if ($validated['puskesmas_name'] !== $puskesmasName) {
                    throw new \Exception("Data puskesmas tidak cocok.");
                }
            }

            // --- FETCH ULANG TARGET ---
            $authoritativeTargets = [];
            if ($jenisLaporan === 'capaian_program') {
                $authoritativeTargets = $this->getRefreshedTargets($puskesmasName, $validated['tahun']);
            }
            
            $indicators = $this->getIndicators($jenisLaporan); 

            $laporan = LaporanKinerja::firstOrNew([
                'puskesmas_name' => $puskesmasName, 
                'tahun' => $validated['tahun'],
                'jenis_laporan' => $jenisLaporan 
            ]);
            
            $laporan->keterangan = $validated['keterangan'] ?? null;
            $laporan->save();
            
            if ($laporan->wasRecentlyCreated === false) { $laporan->details()->delete(); }
            
            $detailsData = [];
            foreach ($indicators as $index => $indicatorName) {
                $detailInput = $validated['details'][$index] ?? null;
                if (!$detailInput) { continue; }
                
                $trimmedName = trim($indicatorName);
                
                // Skip judul Labkesda
                $isMainTitleLab = $jenisLaporan === 'labkesda_capaian' && !Str::contains($trimmedName, ['.', '-']);
                $isSubSectionLab = $jenisLaporan === 'labkesda_capaian' && preg_match('/^[a-z]\./', $trimmedName);
                if ($isMainTitleLab || $isSubSectionLab) { continue; }
    
                // --- LOGIKA TARGET (AUTO FILL) ---
                $targetValue = null;
                if ($jenisLaporan === 'capaian_program') {
                    if (isset($authoritativeTargets[$trimmedName])) {
                        $targetValue = $authoritativeTargets[$trimmedName];
                    } 
                    if ($targetValue === null || $targetValue == 0) {
                        $targetValue = $detailInput['target_sasaran'] ?? 0;
                    }
                }
                
                $detailsData[] = new KinerjaCapaianDetail([
                    'indikator_name' => $trimmedName, 
                    'target_sasaran' => $targetValue,
                    'bln_1' => $detailInput['bln_1'] ?? 0, 'bln_2' => $detailInput['bln_2'] ?? 0, 'bln_3' => $detailInput['bln_3'] ?? 0,
                    'bln_4' => $detailInput['bln_4'] ?? 0, 'bln_5' => $detailInput['bln_5'] ?? 0, 'bln_6' => $detailInput['bln_6'] ?? 0,
                    'bln_7' => $detailInput['bln_7'] ?? 0, 'bln_8' => $detailInput['bln_8'] ?? 0, 'bln_9' => $detailInput['bln_9'] ?? 0,
                    'bln_10' => $detailInput['bln_10'] ?? 0, 'bln_11' => $detailInput['bln_11'] ?? 0, 'bln_12' => $detailInput['bln_12'] ?? 0,
                ]);
            }
            
            if (!empty($detailsData)) { $laporan->details()->saveMany($detailsData); }
            
            DB::commit();
    
            return redirect()->route('laporan-kinerja.edit', ['id' => $laporan->id])
                             ->with('success', "Laporan berhasil disimpan! Target Sasaran diperbarui otomatis.");
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $laporan = LaporanKinerja::with('details')->findOrFail($id);
        $user = Auth::user();
        if (!$this->canUserAccessLaporan($user, $laporan)) {
            return redirect()->route('laporan-kinerja.user.index')->with('error', 'Akses ditolak.');
        }
        
        $indicators = $this->getIndicators($laporan->jenis_laporan); 
        
        // --- AMBIL TARGET UNTUK HALAMAN EDIT ---
        $savedTargets = [];
        if ($laporan->jenis_laporan === 'capaian_program') {
            $savedTargets = $this->getRefreshedTargets($laporan->puskesmas_name, $laporan->tahun);
        }

        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        $viewName = ($laporan->jenis_laporan === 'labkesda_capaian') ? 'laporan_kinerja.labkesda_edit' : 'laporan_kinerja.edit';
        if (!view()->exists($viewName)) $viewName = 'laporan_kinerja.edit';
        $laporanGrouped = $laporan->details->keyBy('indikator_name');
        
        return view($viewName, compact('laporan', 'indicators', 'puskesmasNames', 'laporanGrouped', 'savedTargets'));
    }

    public function update(Request $request, $id)
    {
       $laporan = LaporanKinerja::findOrFail($id);
       $user = Auth::user();
       if (!$this->canUserAccessLaporan($user, $laporan)) {
          return redirect()->route('laporan-kinerja.user.index')->with('error', 'Akses ditolak.');
       }
       return $this->store($request);
    }

    public function destroy($id)
    {
        try {
            $laporan = LaporanKinerja::findOrFail($id);
            $user = Auth::user();
            if (!$this->canUserAccessLaporan($user, $laporan)) {
                return redirect()->route('laporan-kinerja.user.index')->with('error', 'Akses ditolak.');
            }
            $laporan->delete(); 
            return redirect()->back()->with('success', "Laporan berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus laporan.');
        }
    }

    private function canUserAccessLaporan($user, $laporan)
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'labkesda') return $laporan->jenis_laporan === 'labkesda_capaian';
        return $user->nama_puskesmas === $laporan->puskesmas_name;
    }
}