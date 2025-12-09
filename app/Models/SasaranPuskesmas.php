<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SasaranPuskesmas extends Model
{
    use HasFactory;

    // WAJIB: Mematikan fitur timestamps (created_at dan updated_at) 
    // karena tabel sasaran_puskesmas tidak memilikinya.
    public $timestamps = false; 

    protected $fillable = [
        'puskesmas', 
        'tahun',
        'bumil', 
        'bulin', 
        'bbl',
        'balita_ds',
        'pendidikan_dasar',
        'uspro',
        'lansia',
        'hipertensi',
        'dm',
        'odgj_berat',
        'tb',
        'hiv',
        'idl',
        // ... kolom lain yang diimpor
    ];
}