<?php
    
    
namespace App\Models; 
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    // Pastikan nama class benar
    class KinerjaCapaianDetail extends Model 
    {
        use HasFactory;
    
        protected $table = 'kinerja_capaian_details';
    
        protected $fillable = [
            'laporan_kinerja_id',
            'indikator_name',
            'target_sasaran',
            'bln_1', 'bln_2', 'bln_3', 'bln_4', 
            'bln_5', 'bln_6', 'bln_7', 'bln_8', 
            'bln_9', 'bln_10', 'bln_11', 'bln_12',
        ];
    
        /**
         * Relasi many-to-one: Detail ini milik satu Laporan Kinerja
         */
        public function laporanKinerja()
        {
            return $this->belongsTo(LaporanKinerja::class);
        }
    }
    
