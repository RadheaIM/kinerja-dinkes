<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany; // Pastikan ini ada

    class LaporanKinerja extends Model
    {
        use HasFactory;

        protected $table = 'laporan_kinerja';

        protected $fillable = [
            'puskesmas_name',
            'tahun',
            'jenis_laporan',
            'keterangan', // <-- TAMBAHKAN INI
        ];

        /**
         * Relasi ke detail capaian.
         * Pastikan foreign key benar (default: laporan_kinerja_id)
         * Tambahkan onDelete('cascade') agar detail ikut terhapus saat header dihapus
         */
        public function details(): HasMany
        {
            return $this->hasMany(KinerjaCapaianDetail::class)->orderBy('id'); // Urutkan berdasarkan ID
        }

        // Tambahkan ini jika belum ada, penting untuk cascading delete
        protected static function boot()
        {
            parent::boot();

            static::deleting(function ($laporan) {
                // Hapus semua detail terkait secara manual jika cascade tidak diset di DB/Migration
                 $laporan->details()->delete();
            });
        }
    }
    
