<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PegawaiController; // Meskipun tidak terpakai di file ini, biarkan saja
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKinerjaController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RhkKapusController;
use App\Http\Controllers\SasaranPuskesmasController;
use App\Http\Controllers\AdministrasiTuController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ManajemenUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes(['register' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

Route::resource('manajemen-user', ManajemenUserController::class);

    /*
    |--------------------------------------------------------------------------
    | 📋 RHK KAPUS (Hanya Admin)
    |--------------------------------------------------------------------------
    */
    // Route ini akan membuat: index, create, store, show, edit, update, destroy
    Route::resource('rhk-kapus', RhkKapusController::class);
    
    // Rute 'destroy' kustom (menimpa 'destroy' dari resource)
    Route::delete('/rhk-kapus/{puskesmas_name}/{tahun}', [RhkKapusController::class, 'destroy'])
            ->name('rhk-kapus.destroy');


    /*
    |--------------------------------------------------------------------------
    | 📊 LAPORAN KINERJA
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan-kinerja')->name('laporan-kinerja.')->group(function () {
        Route::get('/', [LaporanKinerjaController::class, 'userIndex'])->name('user.index'); 
        Route::get('/admin', [LaporanKinerjaController::class, 'adminIndex'])->name('admin.index'); 
        Route::get('/create', [LaporanKinerjaController::class, 'create'])->name('create'); 
        Route::get('/create-labkesda', [LaporanKinerjaController::class, 'createLabkesdaForm'])->name('create.labkesda'); 
        Route::post('/', [LaporanKinerjaController::class, 'store'])->name('store'); 
        
        // Rute "show" ({id}) yang sebelumnya kita coba, sudah dihapus 
        // karena kita menggunakan "admin.index" dengan parameter.

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
        
        // ==========================================================
        // === PERBAIKAN DI SINI ===
        // Mengubah 'import.form' menjadi 'importForm' agar cocok
        // ==========================================================
        Route::get('/import-form', [SasaranPuskesmasController::class, 'importForm'])->name('importForm');
        // ==========================================================

        Route::post('/import', [SasaranPuskesmasController::class, 'import'])->name('import');
        Route::get('/', [SasaranPuskesmasController::class, 'index'])->name('index');
        Route::get('/create', [SasaranPuskesmasController::class, 'create'])->name('create');
        Route::post('/', [SasaranPuskesmasController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SasaranPuskesmasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SasaranPuskesmasController::class, 'update'])->name('update');
        Route::delete('/{id}', [SasaranPuskesmasController::class, 'destroy'])->name('destroy');
        Route::get('/export-pdf', [SasaranPuskesmasController::class, 'export-pdf'])->name('export-pdf');
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