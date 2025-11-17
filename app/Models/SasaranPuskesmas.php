<?php
// File: app/Models/SasaranPuskesmas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SasaranPuskesmas extends Model
{
    use HasFactory;
    protected $table = 'sasaran_puskesmas';
    public $timestamps = false; // Tabel kita tidak pakai created_at

    /**
     * ==================================================================
     * === INI PERBAIKANNYA ===
     * ==================================================================
     * Daftar $fillable harus SAMA PERSIS dengan tabel SQL yang baru dibuat
     */
    protected $fillable = [
        'puskesmas',
        'bumil',
        'bulin',
        'bbl',
        'balita_ds', // <-- NAMA KOLOM YANG BENAR (SESUAI DB ANDA)
        'pendidikan_dasar',
        'uspro',
        'lansia',
        'hipertensi',
        'dm',
        'odgj_berat',
        'tb',
        'hiv',
        'idl',
    ];
}