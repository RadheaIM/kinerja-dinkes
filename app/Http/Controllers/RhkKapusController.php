<?php
// File: app/Http/Controllers/RhkKapusController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SasaranPuskesmas;
use App\Models\RhkKapus;
use App\Models\RhkKapusDetail; // Pastikan ini ada
use App\Imports\RhkKapusImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RhkKapusController extends Controller
{
    // Kunci/Tanda unik untuk RHK Master
    const RHK_MASTER_KEY = "PERKIN_MASTER_DOCUMENT";

    // ... (Fungsi create() dan store() Anda sudah benar, tidak diubah) ...
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        return view('rhk_kapus.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'file_perkin' => 'required|file|mimes:xlsx,xls'
        ], [
            'file_perkin.required' => 'Anda belum memilih file Excel.',
            'file_perkin.mimes' => 'File harus berekstensi .xlsx atau .xls.'
        ]);
        $tahun = $validated['tahun'];
        $file = $request->file('file_perkin');
        DB::beginTransaction();
        try {
            set_time_limit(0); 
            RhkKapus::where('tahun', $tahun)
                    ->where('puskesmas_name', self::RHK_MASTER_KEY)
                    ->delete(); 
            Excel::import(new RhkKapusImport(self::RHK_MASTER_KEY, $tahun), $file);
            DB::commit();
            return redirect()->route('rhk-kapus.index') 
                            ->with('success', "Data RHK Kapus Master (Tahun $tahun) berhasil di-import.");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
             return redirect()->back()->withErrors($failures)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal import RHK Kapus master: ' . $e->getMessage());
            return redirect()->back()
                            ->with('error', 'Terjadi kesalahan saat meng-import file. Error: ' . $e->getMessage())
                            ->withInput();
        }
    }


    /**
     * ==================================================================
     * === INI PERBAIKAN LOGIKA 'index' (Kembali ke Rowspan) ===
     * ==================================================================
     * Kita akan mengambil data INDUK (RhkKapus) dan mem-paginate-nya.
     * Data DETAIL (RhkKapusDetail) akan diambil melalui relasi.
     */
    public function index(Request $request)
    {
        // Ambil Opsi Tahun untuk Filter
        $tahunOptions = RhkKapus::where('puskesmas_name', self::RHK_MASTER_KEY)
                                ->distinct()
                                ->orderBy('tahun', 'desc')
                                ->pluck('tahun');
                                
        if($tahunOptions->isEmpty()) {
             $tahunOptions = collect([date('Y')]);
        }
        
        // Ambil Tahun yang dipilih dari request
        $selectedTahun = $request->input('tahun', $tahunOptions->first());

        // Query BARU: Ambil data INDUK (RhkKapus)
        $query = RhkKapus::query()
                    ->with('details') // <-- Eager load relasi 'details'
                    ->where('puskesmas_name', self::RHK_MASTER_KEY)
                    ->where('tahun', $selectedTahun)
                    ->orderBy('id', 'asc'); // Urutkan berdasarkan ID induk
        
        // Paginate data INDUK (misal: 5 grup RHK per halaman)
        $data_induk = $query->paginate(5, ['*'], 'page');
        
        // Kirim data ke view 'show.blade.php'
        return view('rhk_kapus.show', [
            'data_induk' => $data_induk, // <-- Kita kirim data INDUK
            'puskesmas_name' => "Dokumen Master (Sama untuk semua)",
            'tahun' => $selectedTahun,
            'tahunOptions' => $tahunOptions
        ]);
    }
    
    // ... (Fungsi edit() dan destroy() Anda sudah benar, tidak diubah) ...
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        return redirect()->route('rhk-kapus.index')->with('info', 'Fitur Edit RHK Kapus belum diimplementasikan.');
    }
    
    public function destroy(Request $request, $puskesmas_name, $tahun)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }
        if ($puskesmas_name !== self::RHK_MASTER_KEY) {
             return redirect()->back()->with('error', 'Hanya bisa menghapus data master.');
        }
        try {
            RhkKapus::where('puskesmas_name', self::RHK_MASTER_KEY)
                    ->where('tahun', $tahun)
                    ->delete();
            return redirect()->route('rhk-kapus.index')
                            ->with('success', "Data RHK Kapus Master tahun $tahun berhasil dihapus.");
        } catch (\Exception $e) {
             Log::error("Gagal menghapus RHK Kapus Master: " . $e->getMessage());
             return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}