    <?php
    // File: database/migrations/xxxx..._modify_bukti_dukung_columns_in_administrasi_tu_table.php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::table('administrasi_tu', function (Blueprint $table) {
                // Ubah kolom string biasa menjadi TEXT agar bisa menyimpan JSON (array)
                $table->text('link_bukti_dukung')->nullable()->change();
                $table->text('file_bukti_dukung')->nullable()->change();
            });
        }
    
        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
             Schema::table('administrasi_tu', function (Blueprint $table) {
                // Kembalikan ke string jika di-rollback (data mungkin hilang)
                $table->string('link_bukti_dukung', 1000)->nullable()->change();
                $table->string('file_bukti_dukung')->nullable()->change();
            });
        }
    };