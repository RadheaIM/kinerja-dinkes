<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SasaranPuskesmas;
use App\Imports\SasaranPuskesmasImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SasaranPuskesmasController extends Controller
{
    /**
     * Menampilkan daftar Sasaran Puskesmas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'labkesda') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman Sasaran Puskesmas.');
        }
        
        $query = SasaranPuskesmas::query(); 
        
        // ==========================================================
        // === Tangkap dan Terapkan Filter Tahun ===
        // ==========================================================
        $tahunFilter = $request->input('tahun');
        
        if ($tahunFilter) {
            // Kolom 'tahun' sudah dipastikan ada di database
            $query->where('tahun', $tahunFilter); 
        }
        // ==========================================================

        // Filter pencarian (berlaku untuk semua role yang bisa akses)
        if ($request->has('search')) { 
            $searchTerm = trim($request->search);
            $query->where('puskesmas', 'LIKE', "%".strtolower($searchTerm)."%");
        }

        // ==========================================================
        // === Hitung Total Keseluruhan (Sesuai Filter Tahun & Search) ===
        // ==========================================================
        $totalQuery = clone $query;

        $totals = $totalQuery->select(
            DB::raw('SUM(bumil) as bumil'),
            DB::raw('SUM(bulin) as bulin'),
            DB::raw('SUM(bbl) as bbl'),
            DB::raw('SUM(balita_ds) as balita_ds'),
            DB::raw('SUM(pendidikan_dasar) as pendidikan_dasar'),
            DB::raw('SUM(uspro) as uspro'),
            DB::raw('SUM(lansia) as lansia'),
            DB::raw('SUM(hipertensi) as hipertensi'),
            DB::raw('SUM(dm) as dm'),
            DB::raw('SUM(odgj_berat) as odgj_berat'),
            DB::raw('SUM(tb) as tb'),
            DB::raw('SUM(hiv) as hiv'),
            DB::raw('SUM(idl) as idl')
        )->first();
        // ==========================================================

        // Lanjutkan query asli untuk paginasi
        $laporans = $query->orderBy('puskesmas')->paginate(20);
        
        // Kirim semua variabel yang diperlukan ke view
        return view('laporan_puskesmas.index', compact('laporans', 'totals', 'tahunFilter'));
    }

    /**
     * Menampilkan form import.
     */
    public function importForm()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        return view('laporan_puskesmas.import'); 
    }

    /**
     * Proses import data dari Excel.
     */
    public function import(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'tahun_import' => 'required|integer|min:2020', 
        ]);

        $tahunImport = $request->input('tahun_import');

        DB::beginTransaction(); 
        try {
            SasaranPuskesmas::where('tahun', $tahunImport)->delete(); 
            
            // Asumsi SasaranPuskesmasImport.php sudah memiliki constructor($tahun)
            Excel::import(new SasaranPuskesmasImport($tahunImport), $request->file('file'));
            
            DB::commit(); 
            return redirect()->route('laporan-puskesmas.index')->with('success', 'Data sasaran puskesmas tahun ' . $tahunImport . ' berhasil diimpor!');
        
        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error("Gagal import Sasaran Puskesmas: " . $e->getMessage());
            // Berikan pesan error yang lebih informatif
            return redirect()->back()->with('error', 'IMPORT GAGAL: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') { abort(403); }
        return redirect()->route('laporan-puskesmas.index')->with('error', 'Gunakan fungsi Import.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') { abort(403); }
        return redirect()->route('laporan-puskesmas.index')->with('error', 'Gunakan fungsi Import.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') { abort(403); }
        return redirect()->route('laporan-puskesmas.index')->with('error', 'Fitur Edit tidak tersedia untuk data ini.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') { abort(403); } 
        return redirect()->route('laporan-puskesmas.index')->with('error', 'Fitur Edit tidak tersedia untuk data ini.');
    }

    /**
     * Remove the specified resource from storage. (Penghapusan Satuan)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        
        try {
            $laporan = SasaranPuskesmas::findOrFail($id);
            $laporan->delete();
            return redirect()->route('laporan-puskesmas.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
             // Tangani Foreign Key Constraint untuk hapus satuan
             if (str_contains($e->getMessage(), 'foreign key')) {
                 return redirect()->route('laporan-puskesmas.index')->with('error', 'Gagal menghapus data. Sasaran ini sudah digunakan di Laporan Kinerja.');
             }
             return redirect()->route('laporan-puskesmas.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * === FUNGSI BARU: MENGHAPUS SEMUA SASARAN BERDASARKAN TAHUN ===
     */
    public function destroyByYear(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        // 1. Ambil input tahun.
        $tahun = $request->input('tahun');
        
        // 2. Validasi input tahun jika filter tidak kosong.
        // Jika $tahun kosong (Semua Tahun), kita lewati validasi integer.
        if (!empty($tahun)) {
             $request->validate([
                'tahun' => 'integer|min:2020',
             ]);
        }

        $isAllYears = empty($tahun);
        $messageYear = $isAllYears ? 'SEMUA TAHUN' : "tahun **$tahun**";

        DB::beginTransaction();
        try {
            $query = SasaranPuskesmas::query();
            
            // Hapus semua data hanya jika $tahun tidak ada, atau filter berdasarkan $tahun
            if (!$isAllYears) {
                $query->where('tahun', $tahun);
            }
            
            $count = $query->delete(); // Lakukan penghapusan massal

            DB::commit();
            
            if ($count > 0) {
                return redirect()->route('laporan-puskesmas.index')
                                 ->with('success', "Berhasil menghapus **$count** baris data Sasaran Puskesmas untuk **$messageYear**.");
            } else {
                 return redirect()->route('laporan-puskesmas.index')
                                 ->with('warning', "Tidak ada data Sasaran Puskesmas untuk **$messageYear** yang ditemukan untuk dihapus.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus data massal Sasaran Puskesmas ($messageYear): " . $e->getMessage());
            
            // Tangani Foreign Key Constraint
            if (str_contains($e->getMessage(), 'foreign key')) {
                return redirect()->back()->with('error', 'Gagal menghapus data massal: Data Sasaran '.$messageYear.' **sudah digunakan** dalam Laporan Kinerja. Harap hapus Laporan Kinerja terkait terlebih dahulu.');
            }
            
            return redirect()->back()->with('error', 'Gagal menghapus data massal: ' . $e->getMessage());
        }
    }

    /**
     * Export a PDF document of the resource.
     */
    public function exportPdf(Request $request)
    {
         if (Auth::user()->role !== 'admin') {
             abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
         }
         return redirect()->route('laporan-puskesmas.index')->with('error', 'Fitur PDF belum disesuaikan.');
    }
}