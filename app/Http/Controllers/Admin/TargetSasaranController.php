<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TargetSasaran;
use Illuminate\Support\Facades\DB;

class TargetSasaranController extends Controller
{
    /**
     * Menampilkan halaman input target
     */
    public function create()
    {
        // Ambil daftar user dengan role 'puskesmas' untuk dropdown
        // Kita ambil 'puskesmas_name' jika ada, atau 'name' sebagai fallback
        $listPuskesmas = User::where('role', 'puskesmas')
                            ->orderBy('name')
                            ->get();

        // Daftar Indikator (HARUS SAMA PERSIS dengan LaporanKinerjaController)
        // Sebaiknya ini dibuat config atau tabel master tersendiri kedepannya
        $listIndikator = [
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
            'Pelayanan Kesehatan Orang Dengan Risiko Terinfeksi Virus Yang Melemahkan Daya Tahan Tubuh Manusia (Human Immunodeficiency Virus)',
            'Capaian D/S',
            'Capaian IDL',
            'Capaian IKS',
            'Cek Kesehatan Gratis',
            'Jumlah balita stunting',
            'Kematian Ibu',
            'Kematian Bayi',
            'Jumlah kunjungan Rawat Jalan',
            'Jumlah kunjungan Rawat Inap',
            'Kejadian KLB',
        ];

        return view('admin.target.create', compact('listPuskesmas', 'listIndikator'));
    }

    /**
     * Menyimpan data target ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'puskesmas_name' => 'required|string',
            'tahun' => 'required|integer|digits:4',
            'targets' => 'required|array',
        ]);

        $puskesmasName = $request->puskesmas_name;
        $tahun = $request->tahun;
        $targets = $request->targets; // Array [nama_indikator => nilai_target]

        DB::beginTransaction();
        try {
            foreach ($targets as $indikator => $nilai) {
                // Pastikan nilai tidak null, jika kosong set 0
                $nilaiTarget = $nilai ?? 0;

                // Gunakan updateOrCreate agar tidak duplikat
                // Jika sudah ada data untuk (puskesmas, tahun, indikator) tersebut, update nilainya
                // Jika belum, buat baru
                TargetSasaran::updateOrCreate(
                    [
                        'puskesmas_name' => $puskesmasName,
                        'tahun' => $tahun,
                        'indikator_name' => $indikator
                    ],
                    [
                        'target_value' => $nilaiTarget
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Target Sasaran untuk ' . $puskesmasName . ' tahun ' . $tahun . ' berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    
    /**
     * Mengambil data target via AJAX (untuk mengisi form saat puskesmas dipilih)
     */
    public function getTargets(Request $request)
    {
        $puskesmasName = $request->query('puskesmas_name');
        $tahun = $request->query('tahun');

        if (!$puskesmasName || !$tahun) {
            return response()->json([]);
        }

        $targets = TargetSasaran::where('puskesmas_name', $puskesmasName)
                                ->where('tahun', $tahun)
                                ->pluck('target_value', 'indikator_name');
        
        return response()->json($targets);
    }
}