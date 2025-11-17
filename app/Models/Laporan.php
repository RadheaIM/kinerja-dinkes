<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans'; // pastikan pakai plural, sesuai migrasi

    protected $fillable = [
        'judul',
        'unit',       // puskesmas atau labkesda
        'kategori',   // capaian program / administrasi
        'wilayah',    // nama puskesmas (jika unit puskesmas)
        'file',       // nama file yang diupload
        'pegawai_id', // relasi ke pegawai
    ];

    // Relasi ke tabel pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
