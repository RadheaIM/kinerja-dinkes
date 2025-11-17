<?php
// File: app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanKinerja;
use App\Models\AdministrasiTu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // <-- Tambahkan Log
use Illuminate\Support\Str; // <-- Tambahkan Str

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $tahunIni = Carbon::now()->year;
        
        // Variabel default
        $dataRingkasan = null;
        $laporanKinerja = null;
        $laporanAdminTu = null;
        $namaUnit = null; // <-- Definisikan di luar
        $viewName = 'home'; // Default view untuk admin

        try {
            // =======================================================
            // LOGIKA BARU MENGGUNAKAN 3 ROLE
            // =======================================================
            switch ($user->role) {
                
                // --- KASUS 1: JIKA USER ADALAH 'admin' ---
                case 'admin':
                    $viewName = 'home'; // Tampilan admin

                    // 1. Kinerja Puskesmas
                    $countKinerjaPusk = LaporanKinerja::where('tahun', $tahunIni)
                                            ->where('jenis_laporan', 'capaian_program')
                                            ->where('puskesmas_name', '!=', 'Labkesda')
                                            ->count();

                    // 2. Kinerja Labkesda
                    $countKinerjaLab = LaporanKinerja::where('tahun', $tahunIni)
                                            ->where('jenis_laporan', 'labkesda_capaian')
                                            ->count();

                    // 3. Admin TU Puskesmas
                    $countAdminPusk = AdministrasiTu::where('tahun', $tahunIni)
                                            ->where('jenis_laporan', 'puskesmas')
                                            ->distinct('puskesmas_name')
                                            ->count('puskesmas_name');

                    // 4. Admin TU Labkesda
                    $countAdminLab = AdministrasiTu::where('tahun', $tahunIni)
                                            ->where('jenis_laporan', 'labkesda')
                                            ->exists();

                    $dataRingkasan = [
                        'countKinerjaPusk' => $countKinerjaPusk,
                        'countKinerjaLab' => $countKinerjaLab,
                        'countAdminPusk' => $countAdminPusk,
                        'countAdminLab' => $countAdminLab ? 1 : 0,
                    ];
                    break;

                // --- KASUS 2: JIKA USER ADALAH 'puskesmas' ---
                case 'puskesmas':
                    $viewName = 'user_dashboard';
                    $namaUnit = $user->nama_puskesmas; // PERBAIKAN: nama_puskesmas
                    
                    if (empty($namaUnit)) {
                         throw new \Exception("Profil Puskesmas Anda ({$user->email}) tidak memiliki 'nama_puskesmas' di database.");
                    }

                    // Cek Laporan Kinerja
                    $laporanKinerja = LaporanKinerja::where('puskesmas_name', $namaUnit)
                                                ->where('tahun', $tahunIni)
                                                ->where('jenis_laporan', 'capaian_program')
                                                ->first();
                    // Cek Laporan Admin TU
                    $laporanAdminTu = AdministrasiTu::where('puskesmas_name', $namaUnit)
                                                ->where('tahun', $tahunIni)
                                                ->where('jenis_laporan', 'puskesmas')
                                                ->exists();
                    break;

                // --- KASUS 3: JIKA USER ADALAH 'labkesda' ---
                case 'labkesda':
                    $viewName = 'user_dashboard';
                    $namaUnit = 'Labkesda';
                    
                    // Cek Laporan Kinerja
                    $laporanKinerja = LaporanKinerja::where('puskesmas_name', $namaUnit)
                                                ->where('tahun', $tahunIni)
                                                ->where('jenis_laporan', 'labkesda_capaian')
                                                ->first();
                    // Cek Laporan Admin TU
                    $laporanAdminTu = AdministrasiTu::where('puskesmas_name', $namaUnit)
                                                ->where('tahun', $tahunIni)
                                                ->where('jenis_laporan', 'labkesda')
                                                ->exists();
                    break;

                // --- KASUS 4: DEFAULT (Jika role tidak dikenali) ---
                default:
                    Auth::logout();
                    return redirect('/login')->with('error', 'Peran (Role) Anda tidak dikenali. Hubungi Administrator.');
            }

            // Kirim data ke view yang sesuai
            // PERBAIKAN: Tambahkan 'namaUnit' ke compact
            return view($viewName, compact('user', 'dataRingkasan', 'laporanKinerja', 'laporanAdminTu', 'tahunIni', 'namaUnit'));

        } catch (\Exception $e) {
            // JIKA TERJADI ERROR DATABASE
            Log::error("Gagal memuat dashboard (Role: {$user->role}): " . $e->getMessage());
            
            // Tetap tampilkan halaman, tapi $dataRingkasan akan null
            return view($viewName, compact('user', 'dataRingkasan', 'laporanKinerja', 'laporanAdminTu', 'tahunIni', 'namaUnit'));
        }
    }
}