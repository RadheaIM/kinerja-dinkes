<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jabatan',
        'email',
    ];

    /**
     * Relasi: satu pegawai bisa memiliki banyak laporan
     */
    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}
