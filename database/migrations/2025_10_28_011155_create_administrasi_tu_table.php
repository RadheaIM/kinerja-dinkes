    <?php
    // File: database/migrations/xxxx_xx_xx_xxxxxx_create_administrasi_tu_table.php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('administrasi_tu', function (Blueprint $table) {
                $table->id();
                $table->string('puskesmas_name'); // Nama Puskesmas
                $table->year('tahun');         // Tahun laporan
                $table->string('jenis_layanan_spm')->nullable(); // Kolom B (mis: A, B, C)
                $table->text('indikator');      // Kolom D
                $table->string('target')->nullable();        // Kolom E (bisa teks)
                
                // Kolom Capaian Bulanan (bisa angka atau status 'Ada/Tidak')
                // Kita gunakan string agar fleksibel
                $table->string('bln_1')->nullable(); $table->string('bln_2')->nullable(); $table->string('bln_3')->nullable();
                $table->string('bln_4')->nullable(); $table->string('bln_5')->nullable(); $table->string('bln_6')->nullable();
                $table->string('bln_7')->nullable(); $table->string('bln_8')->nullable(); $table->string('bln_9')->nullable();
                $table->string('bln_10')->nullable(); $table->string('bln_11')->nullable(); $table->string('bln_12')->nullable();
                
                // Kolom Hasil (Total, %) bisa dihitung saat ditampilkan, tidak perlu disimpan
                
                $table->string('link_bukti_dukung')->nullable(); // Kolom T untuk link GDrive/URL
                $table->string('file_bukti_dukung')->nullable(); // Kolom untuk path file jika diupload
                
                $table->timestamps();
                
                // Indeks untuk pencarian cepat
                $table->index(['puskesmas_name', 'tahun']); 
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('administrasi_tu');
        }
    };
    
