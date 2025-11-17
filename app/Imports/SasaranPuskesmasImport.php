<?php
// File: app/Imports/SasaranPuskesmasImport.php

namespace App\Imports;

use App\Models\SasaranPuskesmas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

// Importer ini akan memetakan 1 baris Excel ke 1 baris Database
class SasaranPuskesmasImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public function headingRow(): int
    {
        return 1; // Header ada di baris 1
    }

    public function model(array $row)
    {
        // Pastikan baris memiliki data puskesmas
        if (!isset($row['puskesmas']) || empty($row['puskesmas'])) {
            return null; // Lewati baris kosong
        }

        // ==========================================================
        // === PERBAIKAN: Bersihkan nama Puskesmas saat import ===
        // ==========================================================
        // 1. Ganti spasi "kotor" (non-breaking space) dari Excel/CSV menjadi spasi biasa
        $namaPuskesmasBersih = str_replace("\u{00A0}", ' ', $row['puskesmas']);
        
        // 2. Hapus spasi normal di awal dan akhir
        $namaPuskesmasBersih = trim($namaPuskesmasBersih);
        
        // 3. (TAMBAHAN FINAL) Paksa jadi huruf kecil semua saat disimpan
        $namaPuskesmasBersih = strtolower($namaPuskesmasBersih);
        // ==========================================================

        // === PERBAIKAN BARU: Lewati baris JUMLAH/TOTAL ===
        if ($namaPuskesmasBersih == 'jumlah' || $namaPuskesmasBersih == 'total') {
            return null; // JANGAN IMPORT BARIS INI
        }
        // ================================================
        
        return new SasaranPuskesmas([
            'puskesmas'         => $namaPuskesmasBersih, // <-- Gunakan nama yang SUDAH BERSIH
            'bumil'             => $row['bumil'] ?? 0,
            'bulin'             => $row['bulin'] ?? 0,
            'bbl'               => $row['bbl'] ?? 0,
            'balita_ds'         => $row['balita_ds'] ?? 0,
            'pendidikan_dasar'  => $row['pendidikan_dasar'] ?? 0,
            'uspro'             => $row['uspro'] ?? 0,
            'lansia'            => $row['lansia'] ?? 0,
            'hipertensi'        => $row['hipertensi'] ?? 0,
            'dm'                => $row['dm'] ?? 0,
            'odgj_berat'        => $row['odgj_berat'] ?? 0,
            'tb'                => $row['tb'] ?? 0,
            'hiv'               => $row['hiv'] ?? 0,
            'idl'               => $row['idl'] ?? 0,
        ]);
    }
}