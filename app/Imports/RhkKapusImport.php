<?php
// File: app/Imports/RhkKapusImport.php

namespace App\Imports;

use App\Models\RhkKapus;
use App\Models\RhkKapusDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;

class RhkKapusImport implements ToCollection, WithHeadingRow, WithStartRow
{
    protected $puskesmas_name; // Ini akan berisi "PERKIN_MASTER_DOCUMENT"
    protected $tahun;
    
    // Variabel untuk "mengingat" data Induk saat looping
    private $currentRhkKapus = null; 
    private $currentRhkKadis = '';
    private $currentIndikator = '';

    public function __construct(string $puskesmas_name, int $tahun)
    {
        $this->puskesmas_name = $puskesmas_name;
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            
            // 1. Definisikan nama kolom (sesuai Excel)
            $rhkDinasKey = 'rhk_kepala_dinas'; 
            $rhkKapusKey = 'rhk_kapus';
            $indikatorKey = 'indikator_kinerja';
            $aspekKey = 'aspek';
            $targetKey = 'target_tahunan';
            $rencanaAksiKey = 'rencana_aksi';
            
            // 2. "Mengingat" data dari sel yang digabung (merged)
            if (isset($row[$rhkDinasKey]) && !empty($row[$rhkDinasKey])) {
                $this->currentRhkKadis = $row[$rhkDinasKey];
            }
            if (isset($row[$indikatorKey]) && !empty($row[$indikatorKey])) {
                $this->currentIndikator = $row[$indikatorKey];
            }

            // 3. Membuat Data INDUK (Parent)
            if (isset($row[$rhkKapusKey]) && !empty($row[$rhkKapusKey])) {
                $this->currentRhkKapus = RhkKapus::create([
                    'puskesmas_name' => $this->puskesmas_name, // "PERKIN_MASTER_DOCUMENT"
                    'tahun' => $this->tahun,
                    'rhk_kadis' => $this->currentRhkKadis, // Ambil dari memori
                    'rhk_kapus' => $row[$rhkKapusKey],
                ]);
            }

            // 4. Membuat Data DETAIL (Anak)
            if (isset($row[$aspekKey]) && !empty(trim($row[$aspekKey]))) {
                
                if ($this->currentRhkKapus) {
                    
                    RhkKapusDetail::create([
                        'id_rhk_kapus' => $this->currentRhkKapus->id,
                        'indikator_kinerja' => $this->currentIndikator, // Ambil dari memori
                        'aspek' => $row[$aspekKey],                 // Ambil dari baris ini
                        'target_tahunan' => $row[$targetKey],           // Ambil dari baris ini
                        
                        // ===================================
                        // === INI PERBAIKANNYA ===
                        // ===================================
                        // Jika $row[$rencanaAksiKey] kosong (null),
                        // kita masukkan string kosong ('') agar database tidak error.
                        'rencana_aksi' => $row[$rencanaAksiKey] ?? '', 
                    ]);
                }
            }
        }
    }

    /**
     * Tentukan baris mana yang berisi Heading (Judul Kolom)
     */
    public function startRow(): int
    {
        return 4; // Data mulai di baris 4
    }

    /**
     * Tentukan di baris mana Heading berada
     */
    public function headingRow(): int
    {
        return 3; // Header (NO, RHK KEPALA DINAS, ...) ada di baris 3
    }
}