<?php

use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman depan (selalu bisa dibuka, termasuk saat sudah login)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Form login di halaman depan. Kalau sedang login, sesi lama dikeluarkan dulu
// sehingga bisa masuk dengan akun lain.
Route::post('/masuk', function (\App\Http\Requests\Auth\LoginRequest $request) {
    if (Auth::check()) {
        Auth::guard('web')->logout();
    }

    $request->authenticate();
    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'));
})->name('masuk');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Impor Excel (harus SEBELUM resource dokumen)
    Route::get('/dokumen/import', [ImportController::class, 'form'])->name('dokumen.import');
    Route::get('/dokumen/template', [ImportController::class, 'template'])->name('dokumen.template');
    Route::post('/dokumen/import', [ImportController::class, 'import'])->name('dokumen.import.proses');
    

    // CRUD dokumen (index, create, store, show, edit, update, destroy)
    Route::resource('dokumen', DokumenController::class)
        ->parameters(['dokumen' => 'dokumen']);

    // Clustering
    Route::get('/clustering', [ClusteringController::class, 'index'])->name('clustering.index');
    Route::post('/clustering/proses', [ClusteringController::class, 'proses'])->name('clustering.proses');
    Route::post('/clustering/nama', [ClusteringController::class, 'nama'])->name('clustering.nama');

        // Kelola pengguna (khusus admin, dicek di controller)
    Route::get('/pengguna', [\App\Http\Controllers\PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [\App\Http\Controllers\PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{pengguna}/password', [\App\Http\Controllers\PenggunaController::class, 'password'])->name('pengguna.password');
    Route::put('/pengguna/{pengguna}/role', [\App\Http\Controllers\PenggunaController::class, 'role'])->name('pengguna.role');
    Route::delete('/pengguna/{pengguna}', [\App\Http\Controllers\PenggunaController::class, 'destroy'])->name('pengguna.destroy');
   
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';