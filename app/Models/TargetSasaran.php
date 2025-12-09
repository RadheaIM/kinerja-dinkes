<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetSasaran extends Model
{
    use HasFactory;

    protected $table = 'target_sasarans';

    // === BAGIAN INI SANGAT PENTING ===
    // Daftarkan semua kolom yang boleh diisi lewat form controller
    protected $fillable = [
        'puskesmas_name',
        'tahun',
        'indikator_name',
        'target_value',
    ];
}