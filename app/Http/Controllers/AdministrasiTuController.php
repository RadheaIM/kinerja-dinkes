<?php

namespace App\Http\Controllers;

use App\Models\AdministrasiTu;
use App\Models\SasaranPuskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdministrasiTuController extends Controller
{
    // === INDIKATOR PUSKESMAS (HANYA A-G SESUAI PERMINTAAN TERAKHIR) ===
    private $puskesmasAdminIndicators = [
        // A. Dokumen Perencanaan
        ['jenis' => 'A', 'indikator' => 'Dokumen Perencanaan', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'A', 'indikator' => '1. RUK', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'A', 'indikator' => '2. RPK', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'A', 'indikator' => '3. RSB', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'A', 'indikator' => '4. Pelaksanaan Lokbul', 'deskripsi' => 'terlaksana tiap bulan & Laporan Kegiatan', 'target' => '12 dokumen laporan'],
        ['jenis' => 'A', 'indikator' => '5. Pelaksanaan Lokakarya triwulanan', 'deskripsi' => 'terlaksana tiap tiga bulan & Laporan Kegiatan', 'target' => 'empat dokumen laporan'],
        ['jenis' => 'A', 'indikator' => '6. RBA', 'deskripsi' => 'Tersedia pada (n-1) tahun berjalan', 'target' => '1 dokumen'],
        
        // B. Pengelolaan SDM
        ['jenis' => 'B', 'indikator' => 'Pengelolaan SDM', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'B', 'indikator' => '1. Daftar Urutan Kepangkatan', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'B', 'indikator' => '2. Dokumen Rencana Kebutuhan', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'Ada dan Sesuai'],
        ['jenis' => 'B', 'indikator' => '3. Pemutakhiran Data SiSDMK', 'deskripsi' => 'Apdet Data terakhir', 'target' => 'Ada dan Sesuai'],
        ['jenis' => 'B', 'indikator' => '4. Pemutakhiran Data SiMASN', 'deskripsi' => 'Apdet Data terakhir', 'target' => 'Ada dan Sesuai'],
        ['jenis' => 'B', 'indikator' => '5. Rencana Kenaikan Gaji Berkala', 'deskripsi' => 'Daftar Rencana Kenaikan Gaji Berkala selam satu tahun', 'target' => 'satu dokumen rekap'],
        ['jenis' => 'B', 'indikator' => '6. Rencana Kenaikan Pangkat', 'deskripsi' => 'Daftar Rencana Kenaikan Gaji Berkala selam satu tahun', 'target' => 'Jumlah pengajuan usulan KP satu tahun'],
        ['jenis' => 'B', 'indikator' => '7. Rencana Kenaikan Jenjang', 'deskripsi' => 'Daftar Rencana Kenaikan Gaji Berkala selam satu tahun', 'target' => 'Jumlah pengajuan'],
        ['jenis' => 'B', 'indikator' => '8. Pengajuan Cuti', 'deskripsi' => 'Jumlah pengajuan cuti dalam satu tahun', 'target' => 'Jumlah pengajuan'],
        ['jenis' => 'B', 'indikator' => '9. Pengajuan Pensiun', 'deskripsi' => 'Jumlah pengajuan pensiun dalam satu tahun', 'target' => 'Jumlah pengajuan pensiun satu tahun'],
        ['jenis' => 'B', 'indikator' => '10. Pengajuan Tugas Belajar', 'deskripsi' => 'Jumlah pengajuan pensiun tugas satu tahun', 'target' => 'Jumlah pengajuan tugas belajar satu tahun'], 
        ['jenis' => 'B', 'indikator' => '11. Rekap Kehadiran', 'deskripsi' => 'daftar rekap absensi', 'target' => '12 dokumen laporan'],
        ['jenis' => 'B', 'indikator' => '12. Rencana Pengembangan Kompetensi SDM', 'deskripsi' => null, 'target' => 'satu dokumen'],
        
        // C. Pembinaan Disiplin Pegawai
        ['jenis' => 'C', 'indikator' => 'Pembinaan Disiplin Pegawai', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'C', 'indikator' => '1. Dokumentasi Kegiatan Apel', 'deskripsi' => 'laporan kegiatan apel dalam satu bulan', 'target' => '12 dokumen laporan'],
        ['jenis' => 'C', 'indikator' => '2. BA Pembinaan', 'deskripsi' => 'laporan kegiatan pembinaan pegawai', 'target' => '12 dokumen laporan'],
        
        // D. Pengelolaan Aset dan BMD
        ['jenis' => 'D', 'indikator' => 'Pengelolaan Aset dan BMD', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'D', 'indikator' => '1. Pemutakhiran Data ATISISBADA', 'deskripsi' => 'Apdet Data terakhir', 'target' => '12 dokumen laporan'],
        ['jenis' => 'D', 'indikator' => '2. Pemutakhiran Data ASPAK', 'deskripsi' => 'Apdet Data terakhir', 'target' => '12 dokumen laporan'],
        
        // E. Pelayanan Publik
        ['jenis' => 'E', 'indikator' => 'Pelayanan Publik', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'E', 'indikator' => '1. tata kelola SDM', 'deskripsi' => 'laporan kegiatan pengelolaan SDM', 'target' => '12 dokumen laporan'],
        ['jenis' => 'E', 'indikator' => '2. Tata kelola SAPRAS', 'deskripsi' => 'laporan kegiatan pengelolaan Sapras', 'target' => '12 dokumen laporan'],
        ['jenis' => 'E', 'indikator' => '3. Tata Kelola Sistem', 'deskripsi' => 'laporan kegiatan pengelolaan Sistem', 'target' => '12 dokumen laporan'],
        ['jenis' => 'E', 'indikator' => '4. Survey Kepuasan Masyarakat', 'deskripsi' => 'laporan pelaksananaan SKM', 'target' => '1 dokumen laporan'],
        
        // F. Implementasi ILP
        ['jenis' => 'F', 'indikator' => 'Implementasi ILP', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'F', 'indikator' => '1. Penggunaan RME', 'deskripsi' => null, 'target' => 'Ada'],
        
        // G. Korespondensi / Kearsipan
        ['jenis' => 'G', 'indikator' => 'Korespondensi / Kearsipan', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'G', 'indikator' => '1. Jumlah Surat Masuk', 'deskripsi' => 'Rekap Laporan', 'target' => 'Jumlah'],
        ['jenis' => 'G', 'indikator' => '2. Jumlah Surat Keluar', 'deskripsi' => 'Rekap Laporan', 'target' => 'Jumlah'],
    ];

    // ... (Indikator Labkesda sama) ...
    private $labkesdaAdminIndicators = [
        // A. Dokumen Perencanaan
        ['jenis' => 'A', 'indikator' => 'Dokumen Perencanaan', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'A', 'indikator' => '1. RUK', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'A', 'indikator' => '2. RPK', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'A', 'indikator' => '3. RSB', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'A', 'indikator' => '4. Pelaksanaan Lokbul', 'deskripsi' => 'terlaksana tiap bulan & Laporan Kegiatan', 'target' => '12 dokumen laporan'],
        ['jenis' => 'A', 'indikator' => '5. Pelaksanaan Lokakarya triwulanan', 'deskripsi' => 'terlaksana tiap tiga bulan & Laporan Kegiatan', 'target' => 'empat dokumen laporan'],
        ['jenis' => 'A', 'indikator' => '6. RBA', 'deskripsi' => 'Tersedia pada (n-1) tahun berjalan', 'target' => '1 dokumen'],
        
        // B. Pengelolaan SDM
        ['jenis' => 'B', 'indikator' => 'Pengelolaan SDM', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'B', 'indikator' => '1. Daftar Urutan Kepangkatan', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'satu Dokumen'],
        ['jenis' => 'B', 'indikator' => '2. Dokumen Rencana Kebutuhan', 'deskripsi' => 'Ada dan Sesuai Ketentuan', 'target' => 'Ada dan Sesuai'],
        ['jenis' => 'B', 'indikator' => '3. Pemutakhiran Data SiSDMK', 'deskripsi' => 'Apdet Data terakhir', 'target' => 'Ada dan Sesuai'],
        ['jenis' => 'B', 'indikator' => '4. Pemutakhiran Data SiMASN', 'deskripsi' => 'Apdet Data terakhir', 'target' => 'Ada dan Sesuai'],
        ['jenis' => 'B', 'indikator' => '5. Rencana Kenaikan Gaji Berkala', 'deskripsi' => 'Daftar Rencana Kenaikan Gaji Berkala selam satu tahun', 'target' => 'satu dokumen rekap'],
        ['jenis' => 'B', 'indikator' => '6. Rencana Kenaikan Pangkat', 'deskripsi' => 'Daftar Rencana Kenaikan Gaji Berkala selam satu tahun', 'target' => 'Jumlah pengajuan usulan KP satu tahun'],
        ['jenis' => 'B', 'indikator' => '7. Rencana Kenaikan Jenjang', 'deskripsi' => 'Daftar Rencana Kenaikan Gaji Berkala selam satu tahun', 'target' => 'Jumlah pengajuan'],
        ['jenis' => 'B', 'indikator' => '8. Pengajuan Cuti', 'deskripsi' => 'Jumlah pengajuan cuti dalam satu tahun', 'target' => 'Jumlah pengajuan'],
        ['jenis' => 'B', 'indikator' => '9. Pengajuan Pensiun', 'deskripsi' => 'Jumlah pengajuan pensiun dalam satu tahun', 'target' => 'Jumlah pengajuan pensiun satu tahun'],
        ['jenis' => 'B', 'indikator' => '10. Pengajuan Tugas Belajar', 'deskripsi' => 'Jumlah pengajuan pensiun tugas satu tahun', 'target' => 'Jumlah pengajuan tugas belajar satu tahun'], 
        ['jenis' => 'B', 'indikator' => '11. Rekap Kehadiran', 'deskripsi' => 'daftar rekap absensi', 'target' => '12 dokumen laporan'],
        ['jenis' => 'B', 'indikator' => '12. Rencana Pengembangan Kompetensi SDM', 'deskripsi' => null, 'target' => 'satu dokumen'],
        
        // C. Pembinaan Disiplin Pegawai
        ['jenis' => 'C', 'indikator' => 'Pembinaan Disiplin Pegawai', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'C', 'indikator' => '1. Dokumentasi Kegiatan Apel', 'deskripsi' => 'laporan kegiatan apel dalam satu bulan', 'target' => '12 dokumen laporan'],
        ['jenis' => 'C', 'indikator' => '2. BA Pembinaan', 'deskripsi' => 'laporan kegiatan pembinaan pegawai', 'target' => '12 dokumen laporan'],
        
        // D. Pengelolaan Aset dan BMD
        ['jenis' => 'D', 'indikator' => 'Pengelolaan Aset dan BMD', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'D', 'indikator' => '1. Pemutakhiran Data ATISISBADA', 'deskripsi' => 'Apdet Data terakhir', 'target' => '12 dokumen laporan'],
        ['jenis' => 'D', 'indikator' => '2. Pemutakhiran Data ASPAK', 'deskripsi' => 'Apdet Data terakhir', 'target' => '12 dokumen laporan'],
        
        // E. Pelayanan Publik
        ['jenis' => 'E', 'indikator' => 'Pelayanan Publik', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'E', 'indikator' => '1. tata kelola SDM', 'deskripsi' => 'laporan kegiatan pengelolaan SDM', 'target' => '12 dokumen laporan'],
        ['jenis' => 'E', 'indikator' => '2. Tata kelola SAPRAS', 'deskripsi' => 'laporan kegiatan pengelolaan Sapras', 'target' => '12 dokumen laporan'],
        ['jenis' => 'E', 'indikator' => '3. Tata Kelola Sistem', 'deskripsi' => 'laporan kegiatan pengelolaan Sistem', 'target' => '12 dokumen laporan'],
        ['jenis' => 'E', 'indikator' => '4. Survey Kepuasan Masyarakat', 'deskripsi' => 'laporan pelaksananaan SKM', 'target' => '1 dokumen laporan'],
        
        // F. Implementasi ILP
        ['jenis' => 'F', 'indikator' => 'Implementasi ILP', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'F', 'indikator' => '1. Penggunaan RME', 'deskripsi' => null, 'target' => 'Ada'],
        
        // G. Korespondensi / Kearsipan
        ['jenis' => 'G', 'indikator' => 'Korespondensi / Kearsipan', 'deskripsi' => null, 'target' => null],
        ['jenis' => 'G', 'indikator' => '1. Jumlah Surat Masuk', 'deskripsi' => 'Rekap Laporan', 'target' => 'Jumlah'],
        ['jenis' => 'G', 'indikator' => '2. Jumlah Surat Keluar', 'deskripsi' => 'Rekap Laporan', 'target' => 'Jumlah'],
    ];

    /**
    * Tentukan indikator yang akan digunakan berdasarkan jenis laporan.
    */
    private function getIndicators($jenisLaporan)
    {
        return ($jenisLaporan === 'labkesda') ? $this->labkesdaAdminIndicators : $this->puskesmasAdminIndicators;
    }

    /**
    * PERBAIKAN: Fungsi helper baru untuk memeriksa hak akses.
    */
    private function canUserAccess($user, $puskesmasName)
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'labkesda') {
            return strtolower($puskesmasName) === 'labkesda';
        }

        if ($user->role === 'puskesmas') {
            return $user->nama_puskesmas === $puskesmasName;
        }

        return false;
    }

    /**
    * PERBAIKAN: Fungsi index() yang "pintar"
    */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));
        $puskesmas = $request->input('puskesmas');
        $jenisLaporanFilter = 'puskesmas';
        
        $puskesmasNames = collect();
        $query = AdministrasiTu::where('tahun', $tahun);

        if ($user->role === 'admin') {
            $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
            $jenisLaporanFilter = $request->input('jenis_laporan', 'puskesmas');

            if ($puskesmas) {
                $query->where('puskesmas_name', $puskesmas);
            }
            
            if (strtolower($puskesmas) === 'labkesda') {
                $jenisLaporanFilter = 'labkesda';
            } else {
                $query->where('jenis_laporan', $jenisLaporanFilter);
            }
            
            $dropdownJenis = $jenisLaporanFilter; 
            if ($dropdownJenis === 'labkesda') {
                $puskesmasNames = collect(['Labkesda']);
                if (empty($puskesmas)) {
                    $puskesmas = 'Labkesda';
                }
                $query->where('puskesmas_name', 'Labkesda');
            }

        } else {
            if ($user->role === 'labkesda') {
                $puskesmas = 'Labkesda';
                $jenisLaporanFilter = 'labkesda';
            } else {
                $puskesmas = $user->nama_puskesmas;
                $jenisLaporanFilter = 'puskesmas';
            }

            if (empty($puskesmas)) {
                Log::error("User {$user->email} (role: {$user->role}) memiliki 'nama_puskesmas' yang kosong.");
                $laporanGrouped = collect();
                $availableYears = collect([date('Y')]);
                $indicators = $this->getIndicators($jenisLaporanFilter);
                return view('administrasi_tu.index', compact('laporanGrouped', 'puskesmasNames', 'availableYears', 'tahun', 'puskesmas', 'indicators', 'jenisLaporanFilter'))
                    ->with('error_manual', 'Profil Anda tidak memiliki "nama_puskesmas". Harap hubungi Administrator.');
            }
            
            $query->where('puskesmas_name', $puskesmas)
                  ->where('jenis_laporan', $jenisLaporanFilter);
        }

        $laporanGrouped = $query->orderBy('id')->get()->keyBy('indikator');
        
        $availableYearsQuery = AdministrasiTu::distinct()->orderBy('tahun', 'desc');
        if($user->role !== 'admin') {
            $availableYearsQuery->where('puskesmas_name', $puskesmas);
        }
        $availableYears = $availableYearsQuery->pluck('tahun');
        
        $currentYear = date('Y');
        if ($availableYears->isEmpty() || !$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }

        $indicators = $this->getIndicators($jenisLaporanFilter);
        
        return view('administrasi_tu.index', compact(
            'laporanGrouped', 
            'puskesmasNames',
            'availableYears', 
            'tahun', 
            'puskesmas',
            'indicators', 
            'jenisLaporanFilter'
        ));
    }

    /**
    * PERBAIKAN: Fungsi create() yang "pintar"
    */
    public function create(Request $request)
    {
        $user = Auth::user();
        $jenisLaporan = 'puskesmas';
        $selectedTahun = $request->query('tahun', date('Y'));

        if ($user->role === 'admin') {
            $selectedPuskesmas = $request->query('puskesmas');
            $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        } else {
            $selectedPuskesmas = $user->nama_puskesmas;
            $puskesmasNames = collect([$user->nama_puskesmas]);
        }

        $indicators = $this->getIndicators($jenisLaporan);
        
        if ($selectedPuskesmas) {
            $existingCount = AdministrasiTu::where('puskesmas_name', $selectedPuskesmas)
                ->where('tahun', $selectedTahun)
                ->where('jenis_laporan', $jenisLaporan)
                ->count();
            
            if ($existingCount > 0) {
                return redirect()->route('administrasi-tu.edit', [
                    'puskesmas' => $selectedPuskesmas,
                    'tahun' => $selectedTahun,
                ])
                ->with('info', "Data Administrasi & TU untuk $selectedPuskesmas tahun $selectedTahun sudah ada. Anda bisa mengeditnya di sini.");
            }
        }
        
        $laporanGrouped = collect();
        return view('administrasi_tu.form', compact('indicators', 'puskesmasNames', 'selectedPuskesmas', 'selectedTahun', 'jenisLaporan', 'laporanGrouped'));
    }

    /**
    * Show the form for creating a new resource (Labkesda).
    */
    public function createLabkesdaForm(Request $request)
    {
        $selectedPuskesmas = 'Labkesda';
        $selectedTahun = $request->query('tahun', date('Y'));
        $jenisLaporan = 'labkesda';
        $indicators = $this->getIndicators($jenisLaporan);

        $existingCount = AdministrasiTu::where('puskesmas_name', $selectedPuskesmas)
            ->where('tahun', $selectedTahun)
            ->where('jenis_laporan', $jenisLaporan)
            ->count();

        if ($existingCount > 0) {
            return redirect()->route('administrasi-tu.edit', [
                'puskesmas' => $selectedPuskesmas,
                'tahun' => $selectedTahun,
            ])
            ->with('info', "Data Administrasi & TU untuk Labkesda tahun $selectedTahun sudah ada. Anda bisa mengeditnya di sini.");
        }
        
        $puskesmasNames = collect(); 
        $laporanGrouped = collect();
        return view('administrasi_tu.form', compact('indicators', 'selectedPuskesmas', 'selectedTahun', 'jenisLaporan', 'laporanGrouped', 'puskesmasNames'));
    }


    /**
     * PERBAIKAN: Fungsi store() yang aman
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'puskesmas_name' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2020|max:2099',
            'jenis_laporan' => ['required', 'string', Rule::in(['puskesmas', 'labkesda'])],
            'indikators' => 'required|array',
            'indikators.*.capaian.*' => 'nullable|string|max:50',
            'indikators.*.link_bukti_dukung' => 'nullable|string|max:2000',
            'indikators.*.file_bukti_dukung' => 'nullable|array',
            'indikators.*.file_bukti_dukung.*' => 'nullable|file|mimes:pdf,jpg,png,jpeg,doc,docx,xls,xlsx|max:5048',
            'indikators.*.hapus_file' => 'nullable|array',
            'indikators.*.hapus_file.*' => 'nullable|string',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            
            // LOGIKA KEAMANAN NAMA PUSKESMAS
            $puskesmasName = '';
            
            if ($user->role === 'admin') {
                $puskesmasName = $validated['puskesmas_name'];
            } else {
                if (empty($user->nama_puskesmas)) {
                    throw new \Exception("Profil Anda ({$user->email}) tidak memiliki 'nama_puskesmas' di database. Harap hubungi administrator.");
                }
                if ($user->role === 'labkesda') {
                    $puskesmasName = 'Labkesda';
                } else {
                    $puskesmasName = $user->nama_puskesmas;
                }
                if ($validated['puskesmas_name'] !== $puskesmasName) {
                    throw new \Exception("Penyimpanan gagal. Data puskesmas form ({$validated['puskesmas_name']}) tidak cocok dengan profil Anda ({$puskesmasName}).");
                }
            }
            
            $puskesmas = $puskesmasName;
            $tahun = $validated['tahun'];
            $jenisLaporan = $validated['jenis_laporan'];
            $indicatorsList = $this->getIndicators($jenisLaporan);
            $minIndicators = count($indicatorsList);

            if (count($validated['indikators']) < $minIndicators) {
                throw new \Exception("Data indikator tidak lengkap. Harusnya ada $minIndicators, diterima " . count($validated['indikators']));
            }
            
            // --- HAPUS DATA LAMA SEBELUM INPUT BARU AGAR BERSIH ---
            // Kita tidak perlu menghapus file fisiknya, karena di loop bawah akan dicek ulang
            // Tapi untuk amannya kita pakai updateOrCreate di bawah, jadi baris delete ini opsional
            // Jika mau benar-benar reset, uncomment baris delete di bawah.
            /*
            AdministrasiTu::where('puskesmas_name', $puskesmas)
                            ->where('tahun', $tahun)
                            ->where('jenis_laporan', $jenisLaporan)
                            ->delete();
            */

            foreach (array_slice($validated['indikators'], 0, $minIndicators) as $index => $inputData) {
                $indicatorDetail = $indicatorsList[$index] ?? null;
                if (!$indicatorDetail) {
                    continue;
                }
                $indikatorName = $indicatorDetail['indikator'];
                $jenisLayanan = $indicatorDetail['jenis'] ?? null;
                $target = $indicatorDetail['target'] ?? null;
                
                // Ambil existing item (untuk file/link yang mungkin dipertahankan/dihapus)
                $existingItem = AdministrasiTu::where([
                    'puskesmas_name' => $puskesmas,
                    'tahun' => $tahun,
                    'jenis_laporan' => $jenisLaporan,
                    'indikator' => $indikatorName,
                ])->first() ?? new AdministrasiTu();


                $existingFilePaths = is_array($existingItem->file_bukti_dukung ?? null) ? $existingItem->file_bukti_dukung : ($existingItem->file_bukti_dukung ? (array) $existingItem->file_bukti_dukung : []);
                $filesToDelete = $inputData['hapus_file'] ?? [];
                if (!empty($filesToDelete)) {
                    foreach ($filesToDelete as $fileToDeletePath) {
                        if ($fileToDeletePath && Storage::disk('public')->exists($fileToDeletePath)) {
                            Storage::disk('public')->delete($fileToDeletePath);
                        }
                        $existingFilePaths = array_values(array_filter($existingFilePaths, fn($p) => $p !== $fileToDeletePath));
                    }
                }
                $newFilePaths = [];
                if ($request->hasFile("indikators.$index.file_bukti_dukung")) {
                    foreach ($request->file("indikators.$index.file_bukti_dukung") as $file) {
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $safeName = Str::slug($originalName) . '_' . time() . '_' . Str::random(6) . '.' . $extension;
                        $dirPath = "bukti_dukung_admin_tu/{$tahun}/" . Str::slug($puskesmas);
                        $path = $file->storeAs($dirPath, $safeName, 'public');
                        $newFilePaths[] = $path;
                    }
                }
                $finalFilePaths = array_merge(array_values($existingFilePaths), $newFilePaths);
                $linkString = $inputData['link_bukti_dukung'] ?? '';
                $finalLinks = array_filter(
                    array_map(
                        'trim',
                        explode("\n", str_replace(["\r\n", "\r"], "\n", $linkString))
                    )
                );
                
                // Simpan data
                $dataToSave = [
                    'puskesmas_name' => $puskesmas,
                    'tahun' => $tahun,
                    'jenis_laporan' => $jenisLaporan,
                    'jenis_layanan_spm' => $jenisLayanan,
                    'indikator' => $indikatorName,
                    'target' => $target,
                    'link_bukti_dukung' => $finalLinks,
                    'file_bukti_dukung' => $finalFilePaths,
                ];

                for ($i = 1; $i <= 12; $i++) {
                    // Note: form sends capaian keys as angka 1..12
                    $dataToSave['bln_' . $i] = isset($inputData['capaian'][$i]) ? $inputData['capaian'][$i] : null;
                }
                
                // MENGGUNAKAN updateOrCreate UNTUK KEPASTIAN
                AdministrasiTu::updateOrCreate(
                    [
                        'puskesmas_name' => $puskesmas,
                        'tahun' => $tahun,
                        'jenis_laporan' => $jenisLaporan,
                        'indikator' => $indikatorName,
                    ],
                    $dataToSave
                );
            }
            
            DB::commit();

            return redirect()->route('administrasi-tu.edit', [
                                    'puskesmas' => $puskesmas, 
                                    'tahun' => $tahun
                                   ])
                                   ->with('success', "Data Administrasi & TU untuk $puskesmas tahun $tahun berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan data Administrasi & TU: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            $errorMessage = 'Gagal menyimpan data. Terjadi kesalahan: ' . $e->getMessage();
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * PERBAIKAN: Fungsi destroy() UNTUK HAPUS MASSAL PER PUSKESMAS & TAHUN
     * Logika ini menggantikan penghapusan per ID baris, karena tabel rekap menampilkan per puskesmas.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Di sini $id sebenarnya adalah NAMA PUSKESMAS yang dikirim dari route
            $namaPuskesmas = $id; 
            
            // Ambil tahun dari request (dikirim lewat hidden input form delete)
            // Jika tidak ada, default ke tahun sekarang (tapi sebaiknya selalu kirim dari form)
            $tahun = $request->input('tahun', date('Y'));

            // Cek Hak Akses sebelum menghapus
            if (!$this->canUserAccess($user, $namaPuskesmas)) {
                return redirect()->back()->with('error', 'Anda tidak diizinkan menghapus data unit kerja lain.');
            }

            // --- LOGIKA HAPUS FILE FISIK DULU ---
            // Kita cari semua data milik puskesmas & tahun tsb untuk hapus file-nya
            $itemsToDelete = AdministrasiTu::where('puskesmas_name', $namaPuskesmas)
                                           ->where('tahun', $tahun)
                                           ->get();
            
            if ($itemsToDelete->isEmpty()) {
                return redirect()->back()->with('error', "Data untuk $namaPuskesmas tahun $tahun tidak ditemukan.");
            }

            foreach ($itemsToDelete as $item) {
                 $files = is_array($item->file_bukti_dukung ?? null) ? $item->file_bukti_dukung : ($item->file_bukti_dukung ? (array) $item->file_bukti_dukung : []);
                 foreach ($files as $f) {
                     if ($f && Storage::disk('public')->exists($f)) {
                         Storage::disk('public')->delete($f);
                     }
                 }
            }

            // --- HAPUS DATA DI DATABASE ---
            $deletedCount = AdministrasiTu::where('puskesmas_name', $namaPuskesmas)
                                          ->where('tahun', $tahun)
                                          ->delete();

            return redirect()->back()->with('success', "Berhasil menghapus seluruh data Administrasi TU untuk $namaPuskesmas tahun $tahun.");

        } catch (\Exception $e) {
            Log::error('Gagal menghapus AdministrasiTu: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
    * PERBAIKAN: Fungsi edit() dengan pemeriksaan keamanan
    */
    public function edit(Request $request, $puskesmas, $tahun)
    {
        // ==========================================================
        // === PERBAIKAN: TAMBAHKAN PEMERIKSAAN KEAMANAN ===
        // ==========================================================
        $user = Auth::user();
        if (!$this->canUserAccess($user, $puskesmas)) {
            return redirect()->route('administrasi-tu.index') // Arahkan ke index
                ->with('error', 'Anda tidak diizinkan mengakses data unit kerja lain.');
        }
        // ==========================================================

        $jenisLaporan = (strtolower($puskesmas) === 'labkesda') ? 'labkesda' : 'puskesmas';
        
        $laporanGrouped = AdministrasiTu::where('puskesmas_name', $puskesmas)
            ->where('tahun', $tahun)
            ->where('jenis_laporan', $jenisLaporan)
            ->orderBy('id')
            ->get()
            ->keyBy('indikator');

        // Cek apakah data benar-benar ada
        if ($laporanGrouped->isEmpty()) {
            $route = ($jenisLaporan === 'labkesda') ? 'administrasi-tu.create.labkesda' : 'administrasi-tu.create';
            $routeParams = ($jenisLaporan === 'labkesda') ? ['tahun' => $tahun] : ['puskesmas' => $puskesmas, 'tahun' => $tahun];
            
            // Perbaikan: Redirect ke form create yang sesuai dan menampilkan pesan info
            return redirect()->route($route, $routeParams)
                ->with('info', "Data untuk $puskesmas tahun $tahun belum ada. Silakan isi form di bawah ini.");
        }
        
        $indicators = $this->getIndicators($jenisLaporan);
        $selectedPuskesmas = $puskesmas;
        $selectedTahun = $tahun;

        $viewName = 'administrasi_tu.form'; 
        
        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');

        if (!view()->exists($viewName)) {
            Log::error("View $viewName tidak ditemukan.");
            return redirect()->route('rekap.index')->with('error', "Tampilan untuk $jenisLaporan tidak ditemukan.");
        }
        
        return view($viewName, compact('indicators', 'selectedPuskesmas', 'selectedTahun', 'laporanGrouped', 'jenisLaporan', 'puskesmasNames'));
    }

    /**
    * Update the specified resource in storage.
    */
    public function update(Request $request, $puskesmas, $tahun)
    {
        // ==========================================================
        // === PERBAIKAN: TAMBAHKAN PEMERIKSAAN KEAMANAN ===
        // ==========================================================
        $user = Auth::user();
        if (!$this->canUserAccess($user, $puskesmas)) {
            return redirect()->route('administrasi-tu.index') // Arahkan ke index
                ->with('error', 'Anda tidak diizinkan mengubah data unit kerja lain.');
        }
        // ==========================================================

        $jenisLaporanFromRoute = (strtolower($puskesmas) === 'labkesda') ? 'labkesda' : 'puskesmas';
        if ($request->input('jenis_laporan') !== $jenisLaporanFromRoute) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: Jenis laporan tidak cocok.');
        }
        if ($request->input('puskesmas_name') !== $puskesmas || $request->input('tahun') != $tahun) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: Data unit atau tahun tidak cocok.');
        }
        return $this->store($request);
    }
}