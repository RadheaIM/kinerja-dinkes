<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PegawaiController; 
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKinerjaController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RhkKapusController;
use App\Http\Controllers\SasaranPuskesmasController;
use App\Http\Controllers\AdministrasiTuController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ManajemenUserController;
use App\Http\Controllers\TargetController; // Untuk rute API
use App\Http\Controllers\Admin\TargetSasaranController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Default
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Otentikasi Laravel (Login/Register)
Auth::routes(['register' => true]);

// Dashboard Home
Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

// Grup Rute yang memerlukan Otentikasi (Middleware 'auth')
Route::middleware(['auth'])->group(function () {
    
    // =======================================================================
    // ⚙️ RUTE API (Diakses oleh AJAX/Frontend)
    // =======================================================================
    Route::get('/api/get-targets', [TargetSasaranController::class, 'getTargets'])->name('api.get-targets');


    Route::get('/api/target-sasaran/{puskesmasId}', [TargetController::class, 'getTargetsByPuskesmas'])
        ->name('api.targets.by.puskesmas');

    /*
    |--------------------------------------------------------------------------
    | 👤 MANAJEMEN USER & TARGET (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:admin-access'])->group(function () {
        // Managemen User
        Route::resource('manajemen-user', ManajemenUserController::class);
        
         // Manajemen Target Sasaran (Fitur Baru)
        Route::get('/manajemen-targets', [TargetSasaranController::class, 'create'])->name('target.create');
        Route::post('/manajemen-targets', [TargetSasaranController::class, 'store'])->name('target.store');
    });
    
    // --- Lanjutan Rute Lain (Tetap di dalam Middleware 'auth') ---

    /*
    |--------------------------------------------------------------------------
    | 📋 RHK KAPUS (Semua User)
    |--------------------------------------------------------------------------
    */
    Route::resource('rhk-kapus', RhkKapusController::class);
    Route::delete('/rhk-kapus/{puskesmas_name}/{tahun}', [RhkKapusController::class, 'destroy'])
                 ->name('rhk-kapus.destroy.custom'); 

    /*
    |--------------------------------------------------------------------------
    | 📊 LAPORAN KINERJA (User & Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan-kinerja')->name('laporan-kinerja.')->group(function () {
        Route::get('/', [LaporanKinerjaController::class, 'userIndex'])->name('user.index'); 
        Route::get('/admin', [LaporanKinerjaController::class, 'adminIndex'])->name('admin.index'); 
        Route::get('/create', [LaporanKinerjaController::class, 'create'])->name('create'); 
        Route::get('/create-labkesda', [LaporanKinerjaController::class, 'createLabkesdaForm'])->name('create.labkesda'); 
        Route::post('/', [LaporanKinerjaController::class, 'store'])->name('store'); 
        
        Route::get('/{id}/edit', [LaporanKinerjaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LaporanKinerjaController::class, 'update'])->name('update');
        Route::delete('/{id}', [LaporanKinerjaController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 🏥 SASARAN PUSKESMAS (Hanya Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan/puskesmas')->name('laporan-puskesmas.')->group(function () {
        // Form dan Logika Import Data
        Route::get('/import-form', [SasaranPuskesmasController::class, 'importForm'])->name('importForm');
        Route::post('/import', [SasaranPuskesmasController::class, 'import'])->name('import');

        // =========================================================================
        // === FIX URUTAN ROUTE: RUTE SPESIFIK HARUS DI ATAS RUTE PARAMETER (/{id}) ===
        // =========================================================================
        Route::delete('/destroy-by-year', [SasaranPuskesmasController::class, 'destroyByYear'])
             ->name('destroyByYear'); // <-- RUTE SPESIFIK INI HARUS DULUAN
        // =========================================================================

        // CRUD Sasaran Puskesmas
        Route::get('/', [SasaranPuskesmasController::class, 'index'])->name('index');
        Route::get('/create', [SasaranPuskesmasController::class, 'create'])->name('create');
        Route::post('/', [SasaranPuskesmasController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SasaranPuskesmasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SasaranPuskesmasController::class, 'update'])->name('update');
        Route::delete('/{id}', [SasaranPuskesmasController::class, 'destroy'])->name('destroy'); // <-- RUTE PARAMETER INI HARUS TERAKHIR
        
        // Export PDF
        Route::get('/export-pdf', [SasaranPuskesmasController::class, 'exportPdf'])->name('export-pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | 💼 ADMINISTRASI & TATA USAHA (Puskesmas & Labkesda)
    |--------------------------------------------------------------------------
    */
    Route::prefix('administrasi-tu')->name('administrasi-tu.')->group(function() {
        Route::get('/', [AdministrasiTuController::class, 'index'])->name('index');
        Route::get('/create', [AdministrasiTuController::class, 'create'])->name('create');
        Route::get('/create-labkesda', [AdministrasiTuController::class, 'createLabkesdaForm'])->name('create.labkesda');
        Route::post('/', [AdministrasiTuController::class, 'store'])->name('store');
        
        // Edit/Update/Destroy menggunakan parameter {puskesmas} dan {tahun}
        Route::get('/{puskesmas}/{tahun}/edit', [AdministrasiTuController::class, 'edit'])->name('edit');
        Route::put('/{puskesmas}/{tahun}', [AdministrasiTuController::class, 'update'])->name('update');
        Route::delete('/{puskesmas}/{tahun}', [AdministrasiTuController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 📈 REKAP BULANAN (Hanya Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('rekap')->name('rekap.')->group(function () {
        Route::get('/', [RekapController::class, 'index'])->name('index');

        // RUTE DETAIL DAN DOWNLOAD PDF (Menggunakan Model Binding)
        Route::get('/detail/{laporan}', [RekapController::class, 'show'])->name('show');
        Route::get('/download/{laporan}', [RekapController::class, 'downloadPdf'])->name('download');
        
        Route::get('/export', [RekapController::class, 'export'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | 📄 LAPORAN UMUM (Hanya Admin)
    |--------------------------------------------------------------------------
    */
    Route::resource('laporan', LaporanController::class);
    Route::get('/laporan/download/{id}', [LaporanController::class, 'download'])->name('laporan.download');

    /*
    |--------------------------------------------------------------------------
    | 🙍 PROFIL USER
    |--------------------------------------------------------------------------
    */
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil'); 
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit'); 
    Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update'); 

    /*
    |--------------------------------------------------------------------------
    | 🚪 LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});