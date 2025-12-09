<?php

namespace App\Http\Controllers;

use App\Models\LaporanKinerja;
use App\Models\KinerjaCapaianDetail;
use App\Models\SasaranPuskesmas;
use App\Models\TargetSasaran; // <-- PENTING: Model Target Sasaran ditambahkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class LaporanKinerjaController extends Controller
{
    // === INDIKATOR PUSKESMAS (DIPERBARUI - 19 INDIKATOR) ===
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
    // =======================================================

    // Daftar Indikator Labkesda (Lengkap - Tetap Sama)
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
     * === FUNGSI INDEX (ADMIN) --- GANTI NAMA DARI 'index' ke 'adminIndex' ===
     * ==================================================================
     */
    public function adminIndex(Request $request)
    {
        // 1. Ambil Laporan untuk Tabel
        $query = LaporanKinerja::where('jenis_laporan', 'capaian_program'); // Default
        
        $laporanIdGrafik = $request->input('laporan_id');

        $laporans = $query->orderBy('puskesmas_name')->paginate(10); 

        // 2. Logika untuk Chart
        $chartData = null;
        $chartTitle = null;
        $chartYear = null;
        $selectedLaporan = null;

        // Tentukan laporan mana yang akan digambar di grafik
        if ($laporanIdGrafik) {
            // Jika user mengklik "Lihat Tren", gunakan ID dari URL
            $selectedLaporan = LaporanKinerja::with('details')->find($laporanIdGrafik);
        } elseif ($laporans->isNotEmpty()) {
            // Jika halaman baru dimuat, gunakan data pertama dari tabel
            $selectedLaporan = LaporanKinerja::with('details')->find($laporans->first()->id);
        }

        // Jika kita punya laporan untuk digambar
        if ($selectedLaporan) {
            $chartTitle = $selectedLaporan->puskesmas_name;
            $chartYear = $selectedLaporan->tahun;
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            
            // Siapkan palet warna untuk grafik
            $colors = [
                '#3b82f6', '#ef4444', '#22c55e', '#eab308', '#8b5cf6', '#ec4899', 
                '#f97316', '#14b8a6', '#64748b', '#06b6d4', '#d946ef', '#f43f5e',
                '#84cc16', '#10b981', '#0ea5e9', '#6366f1', '#a855f7'
            ];
            
            $chartDatasets = [];
            
            // Loop SEMUA detail dari laporan yang dipilih
            foreach ($selectedLaporan->details as $index => $detail) {
                
                // Ambil data bulanan
                $monthlyData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $monthlyData[] = $detail['bln_'.$i] ?? 0;
                }
                
                // Ambil warna dari palet
                $color = $colors[$index % count($colors)];

                // Masukkan sebagai dataset baru
                $chartDatasets[] = [
                    'label' => $detail->indikator_name, 
                    'data' => $monthlyData,
                    'borderColor' => $color,
                    'backgroundColor' => $color . '33', 
                    'fill' => false, 
                    'tension' => 0.1
                ];
            }

            $chartData = ['labels' => $labels, 'datasets' => $chartDatasets];
        }

        // 3. Arahkan ke view admin_index
        return view('laporan_kinerja.admin_index', compact('laporans', 'chartData', 'chartTitle', 'chartYear', 'selectedLaporan'));
    }

    /**
     * ==================================================================
     * === FUNGSI USER INDEX --- Melihat data Puskesmas/Labkesda sendiri ===
     * ==================================================================
     */
    public function userIndex(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));
        
        $query = LaporanKinerja::where('tahun', $tahun);
        $yearsQuery = LaporanKinerja::distinct()->orderBy('tahun', 'desc');

        // Tentukan filter query berdasarkan role user
        if ($user->role === 'labkesda') {
            // Hardcode pencarian untuk 'Labkesda'
            $query->where('jenis_laporan', 'labkesda_capaian') 
                  ->where('puskesmas_name', 'Labkesda');
            
            // Filter tahun juga di-hardcode
            $yearsQuery->where('jenis_laporan', 'labkesda_capaian')
                       ->where('puskesmas_name', 'Labkesda');

        } else { // Asumsi role 'puskesmas'
            // Gunakan nama_puskesmas dari user
            $query->where('jenis_laporan', 'capaian_program')
                  ->where('puskesmas_name', $user->nama_puskesmas); 
            
            // Filter tahun juga menggunakan nama_puskesmas dari user
            $yearsQuery->where('jenis_laporan', 'capaian_program')
                       ->where('puskesmas_name', $user->nama_puskesmas); 
        }
        
        $laporans = $query->paginate(20);

        // Ambil daftar tahun (dari query yang sudah difilter)
        $availableYears = $yearsQuery->pluck('tahun');
        $currentYear = date('Y');
        if ($availableYears->isEmpty() || !$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }

        return view('laporan_kinerja.user_index', compact('laporans', 'availableYears', 'tahun'));
    }

    /**
     * Menampilkan form untuk membuat laporan Puskesmas - Capaian Program.
     * DIPERBARUI: Mengambil Target Otomatis dengan LOGIKA SMART SEARCH
     */
    public function create(Request $request)
    {
        // 1. Ambil Tahun (Default tahun sekarang jika tidak ada di request)
        $tahun = $request->query('tahun', date('Y'));

        // 2. Tentukan Nama Unit (Puskesmas) untuk mencari Target
        $namaUnit = null;
        if (Auth::user()->role === 'puskesmas') {
            // Ambil dari profil user yang login
            $namaUnit = Auth::user()->nama_puskesmas ?? Auth::user()->name; 
        } else {
            // Jika Admin membuatkan, ambil dari request (jika ada)
            $namaUnit = $request->query('puskesmas');
        }

        // 3. AMBIL DATA TARGET DARI DATABASE DENGAN PENCARIAN CERDAS
        // Kita butuh ini karena mungkin profil user isinya "lembang",
        // tapi admin menyimpannya sebagai "puskesmas lembang" (atau sebaliknya).
        $savedTargets = [];
        
        if ($namaUnit) {
            // PERCOBAAN 1: Cari dengan nama persis
            $savedTargets = TargetSasaran::where('puskesmas_name', $namaUnit)
                            ->where('tahun', $tahun)
                            ->pluck('target_value', 'indikator_name')
                            ->toArray();

            // PERCOBAAN 2: Jika kosong, coba variasi nama
            if (empty($savedTargets)) {
                $altName = '';
                
                // Cek apakah nama mengandung kata 'puskesmas'
                if (stripos($namaUnit, 'puskesmas') === false) {
                    // Jika TIDAK ada kata puskesmas (misal: "lembang"), coba cari "Puskesmas lembang"
                    $altName = 'Puskesmas ' . $namaUnit;
                    
                    // Coba juga variasi huruf kecil semua
                    $altNameLower = 'puskesmas ' . strtolower($namaUnit);
                } else {
                    // Jika ADA kata puskesmas (misal: "Puskesmas Lembang"), coba cari "Lembang" saja
                    // Hapus kata 'puskesmas' dan spasi
                    $altName = trim(str_ireplace('puskesmas', '', $namaUnit));
                }

                // Jalankan query pencarian alternatif (gunakan LIKE agar tidak case sensitive)
                // Kita ambil alternatif yang mungkin cocok
                if ($altName) {
                    $savedTargets = TargetSasaran::where(function($q) use ($altName) {
                                        $q->where('puskesmas_name', 'LIKE', $altName) // Cari yang mirip "Lembang"
                                          ->orWhere('puskesmas_name', 'LIKE', 'puskesmas ' . $altName); // Cari "puskesmas Lembang"
                                    })
                                    ->where('tahun', $tahun)
                                    ->pluck('target_value', 'indikator_name')
                                    ->toArray();
                }
            }
        }

        // 4. Data Pendukung Lainnya
        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        $indicators = $this->getIndicators('capaian_program'); 
        $jenisLaporan = 'capaian_program';

        // 5. Kirim data ke View (termasuk $savedTargets)
        return view('laporan_kinerja.create', compact('indicators', 'puskesmasNames', 'jenisLaporan', 'savedTargets', 'tahun', 'namaUnit'));
    }

    /**
     * Menampilkan form untuk membuat laporan Labkesda - Capaian Program.
     */
    public function createLabkesdaForm(Request $request)
    {
        $indicators = $this->getIndicators('labkesda_capaian');
        $labkesdaName = "Labkesda";
        $jenisLaporan = 'labkesda_capaian';
        return view('laporan_kinerja.labkesda_create', compact('indicators', 'labkesdaName', 'jenisLaporan'));
    }

   /**
     * Menyimpan laporan baru atau memperbarui yang sudah ada.
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
                // User Puskesmas / Labkesda mengambil nama dari profilnya
                if (empty($user->nama_puskesmas)) {
                    throw new \Exception("Profil Anda ({$user->email}) tidak memiliki 'nama_puskesmas' di database. Harap hubungi administrator.");
                }

                if ($user->role === 'labkesda') {
                    $puskesmasName = 'Labkesda';
                    $jenisLaporan = 'labkesda_capaian'; 
                } else {
                    $puskesmasName = $user->nama_puskesmas; 
                    $jenisLaporan = 'capaian_program'; 
                }

                if ($validated['puskesmas_name'] !== $puskesmasName) {
                    throw new \Exception("Penyimpanan gagal. Data puskesmas form ({$validated['puskesmas_name']}) tidak cocok dengan profil Anda ({$puskesmasName}).");
                }
            }

            // Validasi tambahan target_sasaran
            if ($jenisLaporan === 'capaian_program') { 
                $rules['details.*.target_sasaran'] = 'nullable|integer|min:0'; 
                $request->validate(['details.*.target_sasaran' => 'nullable|integer|min:0']);
            }
            
            $indicators = $this->getIndicators($jenisLaporan); 

            $laporan = LaporanKinerja::firstOrNew([
                'puskesmas_name' => $puskesmasName, 
                'tahun' => $validated['tahun'],
                'jenis_laporan' => $jenisLaporan 
            ]);
            
            $laporan->keterangan = $validated['keterangan'] ?? null;
            $laporan->save();
            
            // Hapus detail lama jika ini adalah update
            if ($laporan->wasRecentlyCreated === false) { $laporan->details()->delete(); }
            
            $detailsData = [];
            foreach ($indicators as $index => $indicatorName) {
                $detailInput = $validated['details'][$index] ?? null;
                if (!$detailInput) { Log::warning("Data input tidak ditemukan untuk index $index ($indicatorName) saat menyimpan Laporan Kinerja."); continue; }
                
                $trimmedName = trim($indicatorName);
                
                // Logika untuk skip judul di laporan Labkesda
                $isMainTitleLab = $jenisLaporan === 'labkesda_capaian' && !Str::contains($trimmedName, ['.', '-']);
                $isSubSectionLab = $jenisLaporan === 'labkesda_capaian' && preg_match('/^[a-z]\./', $trimmedName);
                if ($isMainTitleLab || $isSubSectionLab) { continue; }
    
                $targetValue = ($jenisLaporan === 'labkesda_capaian') ? null : ($detailInput['target_sasaran'] ?? null);
                
                $detailsData[] = new KinerjaCapaianDetail([
                    'indikator_name' => $trimmedName, 'target_sasaran' => $targetValue,
                    'bln_1' => $detailInput['bln_1'] ?? 0, 'bln_2' => $detailInput['bln_2'] ?? 0, 'bln_3' => $detailInput['bln_3'] ?? 0,
                    'bln_4' => $detailInput['bln_4'] ?? 0, 'bln_5' => $detailInput['bln_5'] ?? 0, 'bln_6' => $detailInput['bln_6'] ?? 0,
                    'bln_7' => $detailInput['bln_7'] ?? 0, 'bln_8' => $detailInput['bln_8'] ?? 0, 'bln_9' => $detailInput['bln_9'] ?? 0,
                    'bln_10' => $detailInput['bln_10'] ?? 0, 'bln_11' => $detailInput['bln_11'] ?? 0, 'bln_12' => $detailInput['bln_12'] ?? 0,
                ]);
            }
            
            if (!empty($detailsData)) { $laporan->details()->saveMany($detailsData); }
            
            DB::commit();
    
            return redirect()->route('laporan-kinerja.edit', ['id' => $laporan->id])
                             ->with('success', "Laporan Kinerja [" . $puskesmasName . " - " . $validated['tahun'] . "] berhasil disimpan/diperbarui!");
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan laporan kinerja [$jenisLaporan]: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Menampilkan form untuk mengedit laporan yang ada.
     */
    public function edit($id)
    {
        $laporan = LaporanKinerja::with('details')->findOrFail($id);
        
        // --- PERBAIKAN LOGIKA OTORISASI ---
        $user = Auth::user();
        if (!$this->canUserAccessLaporan($user, $laporan)) {
            return redirect()->route('laporan-kinerja.user.index')
                             ->with('error', 'Anda tidak diizinkan mengedit laporan unit kerja lain.');
        }
        // --- AKHIR PERBAIKAN ---
        $indicators = $this->getIndicators($laporan->jenis_laporan); 
        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        $viewName = ($laporan->jenis_laporan === 'labkesda_capaian') ? 'laporan_kinerja.labkesda_edit' : 'laporan_kinerja.edit';
        if (!view()->exists($viewName)) {
             Log::warning("View $viewName tidak ditemukan.");
             if ($laporan->jenis_laporan === 'labkesda_capaian') {
                return redirect()->route('laporan-kinerja.user.index')->with('error', "View untuk edit Laporan Kinerja Labkesda belum tersedia.");
             }
             $viewName = 'laporan_kinerja.edit';
        }
        $laporanGrouped = $laporan->details->keyBy('indikator_name');
        return view($viewName, compact('laporan', 'indicators', 'puskesmasNames', 'laporanGrouped'));
    }

    /**
     * Memperbarui laporan yang ada.
     */
    public function update(Request $request, $id)
    {
       // Cek keamanan sebelum memanggil store
       $laporan = LaporanKinerja::findOrFail($id);
       $user = Auth::user();
       
       if (!$this->canUserAccessLaporan($user, $laporan)) {
          return redirect()->route('laporan-kinerja.user.index')
                           ->with('error', 'Anda tidak diizinkan memperbarui laporan unit kerja lain.');
       }
       
       return $this->store($request);
    }

    /**
     * Menghapus laporan (header dan semua baris detail terkait).
     */
    public function destroy($id)
    {
        try {
            $laporan = LaporanKinerja::findOrFail($id);
            
            // --- PERBAIKAN LOGIKA OTORISASI ---
            $user = Auth::user();
            if (!$this->canUserAccessLaporan($user, $laporan)) {
                return redirect()->route('laporan-kinerja.user.index')
                                 ->with('error', 'Anda tidak diizinkan menghapus laporan unit kerja lain.');
            }
            // --- AKHIR PERBAIKAN ---

            $nama = $laporan->puskesmas_name;
            $tahun = $laporan->tahun;
            $laporan->delete(); 
            
            return redirect()->back()->with('success', "Laporan Kinerja untuk $nama tahun $tahun berhasil dihapus!");

        } catch (\Exception $e) {
            Log::error('Gagal menghapus laporan kinerja ID ' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus laporan. Error: ' . $e->getMessage());
        }
    }
   /**
     * Memeriksa apakah user yang login berhak mengakses laporan tertentu.
     */
    private function canUserAccessLaporan($user, $laporan)
    {
        // 1. Admin boleh mengakses semua laporan
        if ($user->role === 'admin') {
            return true;
        }

        // 2. User 'labkesda' hanya boleh mengakses laporan 'labkesda_capaian'
        if ($user->role === 'labkesda') {
            return $laporan->jenis_laporan === 'labkesda_capaian';
        }

        // 3. User lain (Puskesmas) dicek berdasarkan puskesmas_name
        return $user->nama_puskesmas === $laporan->puskesmas_name;
    }
}