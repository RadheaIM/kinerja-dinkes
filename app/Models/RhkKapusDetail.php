<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RhkKapusDetail extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel jika berbeda dari 'rhk_kapus_details'
     */
    protected $table = 'rhk_kapus_details';

    /**
     * Kolom yang boleh diisi secara massal (mass assignable)
     */
    protected $fillable = [
        'id_rhk_kapus',
        'indikator_kinerja',
        'aspek',
        'target_tahunan',
        'rencana_aksi',
    ];

    /**
     * ==================================================================
     * === INI SAMBUNGAN PENTINGNYA (Opsional tapi bagus) ===
     * ==================================================================
     * Mendefinisikan relasi "dimiliki-oleh" (belongs-to).
     * Satu RhkKapusDetail (Anak) dimiliki oleh SATU RhkKapus (Induk).
     */
    public function rhkKapus()
    {
        // 'id_rhk_kapus' adalah foreign key di tabel ini
        // 'id' adalah primary key di tabel 'rhk_kapus'
        return $this->belongsTo(RhkKapus::class, 'id_rhk_kapus', 'id');
    }
}