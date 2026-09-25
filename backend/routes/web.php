<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotifikasiController;

Route::get('/', function () {
    return redirect()->route('login');
});

// admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // CRUD Alat
    Route::get('/alat',                     [AdminController::class, 'indexAlat'])      ->name('alat.index');
    Route::get('/alat/create',              [AdminController::class, 'createAlat'])     ->name('alat.create');
    Route::post('/alat',                    [AdminController::class, 'storeAlat'])      ->name('alat.store');
    Route::get('/alat/{id}/edit',           [AdminController::class, 'editAlat'])       ->name('alat.edit');
    Route::put('/alat/{id}',                [AdminController::class, 'updateAlat'])     ->name('alat.update');
    Route::delete('/alat/{id}',             [AdminController::class, 'destroyAlat'])    ->name('alat.destroy');
    Route::post('/alat/{alat}/tandai-rusak', [AdminController::class, 'tandaiRusakAlat'])->name('alat.tandai-rusak');
    Route::post('/alat/{alat}/perbaiki',    [AdminController::class, 'perbaikiAlat'])   ->name('alat.perbaiki');

    // CRUD User
    Route::get('/users',            [AdminController::class, 'indexUser'])      ->name('user.index');
    Route::get('/users/create',     [AdminController::class, 'createUser'])     ->name('user.create');
    Route::post('/users',           [AdminController::class, 'storeUser'])      ->name('user.store');
    Route::get('/users/{id}/edit',  [AdminController::class, 'editUser'])       ->name('user.edit');
    Route::put('/users/{id}',       [AdminController::class, 'updateUser'])     ->name('user.update');
    Route::delete('/users/{id}',    [AdminController::class, 'destroyUser'])    ->name('user.destroy');

    // CRUD Kategori
    Route::get('/kategori',                 [AdminController::class, 'indexKategori'])      ->name('kategori.index');
    Route::get('/kategori/create',          [AdminController::class, 'createKategori'])     ->name('kategori.create');
    Route::post('/kategori',                [AdminController::class, 'storeKategori'])      ->name('kategori.store');
    Route::get('/kategori/{id}/edit',       [AdminController::class, 'editKategori'])       ->name('kategori.edit');
    Route::put('/kategori/{id}',            [AdminController::class, 'updateKategori'])     ->name('kategori.update');
    Route::get('/kategori/{kategori}/alat', [AdminController::class, 'alatKategori'])       ->name('kategori.alat');
    Route::delete('/kategori/{id}',         [AdminController::class, 'destroyKategori'])    ->name('kategori.destroy');

    // CRUD Peminjaman
    Route::get('/peminjaman',               [AdminController::class, 'indexPeminjaman'])        ->name('peminjaman.index');
    Route::get('/peminjaman/create',        [AdminController::class, 'createPeminjaman'])       ->name('peminjaman.create');
    Route::post('/peminjaman',              [AdminController::class, 'storePeminjaman'])        ->name('peminjaman.store');
    Route::put('/peminjaman/{id}/status',   [AdminController::class, 'updateStatusPeminjaman']) ->name('peminjaman.updateStatus');
    Route::delete('/peminjaman/{id}',       [AdminController::class, 'destroyPeminjaman'])      ->name('peminjaman.destroy');

    // CRUD Pengembalian
    Route::get('/pengembalian/{id}/create',         [AdminController::class, 'createPengembalian']) ->name('pengembalian.create');
    Route::post('/pengembalian/{id}',               [AdminController::class, 'storePengembalian'])  ->name('pengembalian.store');
    Route::get('/pengembalian',                     [AdminController::class, 'indexPengembalian'])  ->name('pengembalian.index');
    Route::get('/pengembalian/{id}/edit',           [AdminController::class, 'editPengembalian'])   ->name('pengembalian.edit');
    Route::put('/pengembalian/{id}',                [AdminController::class, 'updatePengembalian']) ->name('pengembalian.update');
    Route::delete('/pengembalian/{id}',             [AdminController::class, 'destroyPengembalian'])->name('pengembalian.destroy');

    // Log Aktivitas
    Route::get('/log-aktivitas',                    [AdminController::class, 'indexLogAktivitas'])->name('log_aktivitas.index');
});

// Petugas
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Peminjaman & Persetujuan
    Route::get('/peminjaman',                   [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/setujui',     [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');
    Route::post('/peminjaman/{id}/tolak',       [PetugasController::class, 'tolakPeminjaman'])->name('peminjaman.tolak');
    
    // Pengembalian & Denda
    Route::get('/pengembalian',                 [PetugasController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::get('/pengembalian/{id}/proses',     [PetugasController::class, 'createPengembalian'])->name('pengembalian.create');
    Route::post('/pengembalian/{id}/proses',    [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');

    // Cetak Laporan
    Route::get('/laporan',                      [PetugasController::class, 'indexLaporan'])->name('laporan.index'); 
    Route::get('/laporan/cetak',                [PetugasController::class, 'cetakLaporan'])->name('laporan.cetak');
});

// Peminjam
Route::middleware(['auth', 'role:peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    // Katalog & Pengajuan
    Route::get('/katalog',                   [PeminjamController::class, 'katalogAlat'])->name('katalog');
    Route::post('/peminjaman/ajukan',        [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjaman.ajukan');
    Route::get('/riwayat',                   [PeminjamController::class, 'riwayatPeminjaman'])->name('riwayat');
    Route::post('/pengembalian/ajukan/{id}', [PeminjamController::class, 'ajukanPengembalian'])->name('pengembalian.ajukan');
});

// Route Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|----------------------------------------------------------------------
| Notifikasi & Badge (semua role yang sudah login)
|----------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // TAHAP 3: Lonceng
    Route::get('/notifikasi',                       [NotifikasiController::class, 'index'])             ->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/dibaca',  [NotifikasiController::class, 'tandaiDibaca'])      ->name('notifikasi.dibaca');
    Route::post('/notifikasi/{notifikasi}/baca',    [NotifikasiController::class, 'tandaiDibaca'])      ->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua',           [NotifikasiController::class, 'tandaiSemuaDibaca']) ->name('notifikasi.bacaSemua');

    Route::post('/notifikasi/baca-semua',           [NotifikasiController::class, 'tandaiSemuaDibaca']) ->name('notifikasi.bacaSemua');

    // TAHAP 4: Badge fitur
    Route::get('/badges', [NotifikasiController::class, 'badges'])->name('badges');
});