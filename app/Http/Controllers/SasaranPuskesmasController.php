<?php
// File: app/Http/Controllers/SasaranPuskesmasController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SasaranPuskesmas;
use App\Imports\SasaranPuskesmasImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Pastikan DB di-import
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

        // Filter pencarian (berlaku untuk semua role yang bisa akses)
        if ($request->has('search')) { 
            $searchTerm = trim($request->search);
            $query->where('puskesmas', 'LIKE', "%".strtolower($searchTerm)."%");
        }

        // ==========================================================
        // === PERUBAHAN BARU: Hitung Total Keseluruhan ===
        // ==========================================================
        // 1. Clone query SEBELUM di-paginate, agar totalnya sesuai filter pencarian
        $totalQuery = clone $query;

        // 2. Ambil SUM dari semua kolom yang relevan
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
        )->first(); // first() untuk mengambil 1 baris hasil SUM
        // ==========================================================

        // 3. Lanjutkan query asli untuk paginasi
        $laporans = $query->orderBy('puskesmas')->paginate(20);
        
        // 4. Kirim KEDUA variabel ke view
        return view('laporan_puskesmas.index', compact('laporans', 'totals'));
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
            'file' => 'required|mimes:xlsx,xls'
        ]);

        DB::beginTransaction(); 
        try {
            SasaranPuskesmas::query()->delete(); 
            
            Excel::import(new SasaranPuskesmasImport, $request->file('file'));
            DB::commit(); 
            return redirect()->route('laporan-puskesmas.index')->with('success', 'Data sasaran puskesmas berhasil diimpor!');
        
        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error("Gagal import Sasaran Puskesmas: " . $e->getMessage());
            return redirect()->back()->with('error', 'IMPORT GAGAL: ' . $e->getMessage());
        }
    }

    // ... (Sisa fungsi create, store, edit, update, destroy, exportPdf tidak berubah) ...
    // ... (Pastikan sisa fungsi Anda ada di sini) ...
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
        return redirect()->route('laporan_puskesmas.index')->with('error', 'Fitur Edit tidak tersedia untuk data ini.');
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
     * Remove the specified resource from storage.
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
             return redirect()->route('laporan-puskesmas.index')->with('error', 'Gagal menghapus data.');
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