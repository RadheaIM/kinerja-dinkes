<?php

namespace App\Imports;

use App\Models\SasaranPuskesmas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SasaranPuskesmasImport implements ToCollection, WithHeadingRow
{
    private $tahun;

    public function __construct(int $tahun)
    {
        $this->tahun = $tahun;
    }
    
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        if ($rows->count() > 0) {
            // Hapus data lama untuk tahun yang dipilih sebelum import
            SasaranPuskesmas::where('tahun', $this->tahun)->delete(); 
        }

        foreach ($rows as $row) {
            
            // ----------------------------------------------------
            // KUNCI PERBAIKAN: Melewati baris yang mengandung formula Total
            // Kita juga membuat nilai Puskesmas menjadi huruf kecil (puskesmas) untuk case-insensitivity
            // ----------------------------------------------------
            $puskesmasName = trim($row['puskesmas'] ?? '');

            // Jika nama Puskesmas kosong, atau mengandung kata 'Total', lewati baris ini.
            if (empty($puskesmasName) || strtolower($puskesmasName) === 'total') {
                continue; 
            }
            // ----------------------------------------------------
            
            // INJECT TAHUN DI SINI
            SasaranPuskesmas::create([
                'puskesmas' => $puskesmasName,
                'tahun' => $this->tahun, 
                
                // Nilai kolom numeric akan dicast ke integer/float secara otomatis oleh Laravel/Eloquent.
                'bumil' => $row['bumil'] ?? 0, 
                'bulin' => $row['bulin'] ?? 0, 
                'bbl' => $row['bbl'] ?? 0,
                'balita_ds' => $row['balita_ds'] ?? 0,
                'pendidikan_dasar' => $row['pendidikan_dasar'] ?? 0,
                'uspro' => $row['uspro'] ?? 0,
                'lansia' => $row['lansia'] ?? 0,
                'hipertensi' => $row['hipertensi'] ?? 0,
                'dm' => $row['dm'] ?? 0,
                'odgj_berat' => $row['odgj_berat'] ?? 0,
                'tb' => $row['tb'] ?? 0,
                'hiv' => $row['hiv'] ?? 0,
                'idl' => $row['idl'] ?? 0,
            ]);
        }
    }
    
    public function headingRow(): int
    {
        return 1; 
    }
}