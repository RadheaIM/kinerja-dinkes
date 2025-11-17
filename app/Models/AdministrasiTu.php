<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrasiTu extends Model
{
    use HasFactory;

    // Nama tabel sesuai dengan di database
    protected $table = 'administrasi_tu';

    public $timestamps = true;

    // Kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'puskesmas_name',
        'tahun',
        'jenis_laporan',
        'jenis_layanan_spm',
        'indikator',
        'target',
        'bln_1', 'bln_2', 'bln_3', 'bln_4', 'bln_5', 'bln_6',
        'bln_7', 'bln_8', 'bln_9', 'bln_10', 'bln_11', 'bln_12',
        'link_bukti_dukung',
        'file_bukti_dukung',
    ];

    // Cast otomatis untuk kolom array & teks
    protected $casts = [
        'link_bukti_dukung' => 'array',
        'file_bukti_dukung' => 'array',
        'bln_1' => 'string', 'bln_2' => 'string', 'bln_3' => 'string',
        'bln_4' => 'string', 'bln_5' => 'string', 'bln_6' => 'string',
        'bln_7' => 'string', 'bln_8' => 'string', 'bln_9' => 'string',
        'bln_10' => 'string', 'bln_11' => 'string', 'bln_12' => 'string',
    ];
}
