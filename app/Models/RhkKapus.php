<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhkKapus extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel jika berbeda dari 'rhk_kapus'
     */
    protected $table = 'rhk_kapus';

    /**
     * Kolom yang boleh diisi secara massal (mass assignable)
     */
    protected $fillable = [
        'puskesmas_name',
        'tahun',
        'rhk_kadis',
        'rhk_kapus',
        'id_rhk_kapus', // (Kita izinkan null, tapi tetap bisa diisi jika ada)
        // tambahkan kolom lain jika ada yang perlu diisi oleh Importer
    ];

    /**
     * ==================================================================
     * === INI SAMBUNGAN PENTINGNYA ===
     * ==================================================================
     * Mendefinisikan relasi "satu-ke-banyak" (one-to-many).
     * Satu RhkKapus (Induk) memiliki BANYAK RhkKapusDetail (Anak).
     */
    public function details()
    {
        // 'id_rhk_kapus' adalah foreign key di tabel 'rhk_kapus_details'
        // 'id' adalah primary key di tabel 'rhk_kapus' ini
        return $this->hasMany(RhkKapusDetail::class, 'id_rhk_kapus', 'id');
    }

    /**
     * Menangani penghapusan data anak (details) secara otomatis
     * saat data induk (RhkKapus) dihapus.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($rhkKapus) {
            // Hapus semua 'details' yang terkait sebelum menghapus 'rhkKapus'
            // Ini adalah "cascade delete" versi software.
            $rhkKapus->details()->delete();
        });
    }
}