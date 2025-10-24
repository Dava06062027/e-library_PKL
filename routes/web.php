<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\BukuItemController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubKategoriController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\LokasiRakController;
use App\Http\Controllers\PenerbitController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TataraksController;
use App\Http\Controllers\Admin\PeminjamanController;  // Tambah ini untuk PeminjamanController

Route::get('/', function () {
    return view('welcome');
});

// Semua role bisa akses dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile → semua role bisa akses
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================
// 📌 Member (default user)
// ==========================
Route::middleware(['auth'])->group(function () {
    Route::get('/bukus/search', [BukuController::class, 'search'])->name('bukus.search');
    Route::get('/bukuitems/search', [BukuItemController::class, 'search'])->name('bukuitems.search');
    Route::get('/bukus/{id_buku}/items', [BukuItemController::class, 'searchByBuku'])->name('bukuitems.searchByBuku');
    Route::get('/kategoris/{id}/subkategoris', [BukuController::class, 'searchByKategori'])->name('bukus.searchByKategori');
    Route::get('/sub_kategoris/{id}/bukus', [BukuController::class, 'searchBySubKategori'])->name('bukus.searchBySubKategori');
    Route::get('/raks/{id}/bukuitems', [BukuItemController::class, 'searchByRak'])->name('bukuitems.searchByRak');
    Route::get('/penerbits/{id}/bukus', [BukuController::class, 'searchByPenerbit'])->name('bukus.searchByPenerbit');

    // hanya bisa lihat (index + show)
    Route::resource('bukus', BukuController::class)->only(['index','show']);
    Route::resource('bukuitems', BukuItemController::class)->only(['index','show']);
    Route::resource('kategoris', KategoriController::class)->only(['index','show']);
    Route::resource('sub_kategoris', SubKategoriController::class)->only(['index','show']);
    Route::resource('raks', RakController::class)->only(['index','show']);
    Route::resource('lokasis', LokasiRakController::class)->only(['index','show']);
    Route::resource('penerbits', PenerbitController::class)->only(['index','show']);

    // Tambah untuk member: lihat peminjaman sendiri (opsional, jika ingin)
    Route::get('/peminjamans', [PeminjamanController::class, 'myIndex'])->name('peminjamans.myIndex');  // Method baru di controller untuk filter by user
    Route::get('/peminjamans/{id}', [PeminjamanController::class, 'show'])->name('peminjamans.show');  // Show single
});

// ==========================
// 📌 Officer + Admin
// ==========================
Route::middleware(['auth','isOfficerOrAdmin'])->group(function () {

    // CRUD koleksi
    Route::resource('bukus', BukuController::class)->except(['index','show']);
    Route::resource('bukuitems', BukuItemController::class)->except(['index','show']);
    Route::resource('kategoris', KategoriController::class)->except(['index','show']);
    Route::resource('sub_kategoris', SubKategoriController::class)->except(['index','show']);
    Route::resource('raks', RakController::class)->except(['index','show']);
    Route::resource('lokasis', LokasiRakController::class)->except(['index','show']);
    Route::resource('penerbits', PenerbitController::class)->except(['index','show']);

    // Kelola User (lihat & hapus user)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::delete('/users/destroy-selected', [AdminController::class, 'destroySelected'])->name('users.destroySelected');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::get('/users/online-status', [AdminController::class, 'onlineStatus'])->name('users.onlineStatus');
        Route::get('/users/{user}', [AdminController::class, 'show'])->name('users.show');
        Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::get('tataraks/search-buku-datatable', [TataraksController::class, 'searchBukuDatatable'])->name('tataraks.searchBukuDatatable');
        Route::post('tataraks/bulk', [TataraksController::class, 'bulkStore'])->name('tataraks.bulkStore');

        Route::get('/tataraks/available-eksemplar/{id_buku}', [TataraksController::class, 'availableEksemplarByBuku'])->name('admin.tataraks.availableEksemplarByBuku');
        Route::get('/tataraks/buku-kategori/{id_buku}', [TataraksController::class, 'getBukuKategori'])->name('admin.tataraks.getBukuKategori');
        Route::get('/tataraks/rak-by-kategori', [TataraksController::class, 'getRakByKategori'])->name('admin.tataraks.getRakByKategori');
        Route::get('tataraks/available-items', [TataraksController::class, 'availableItems'])->name('tataraks.available-items');
        Route::delete('tataraks/destroy-selected', [TataraksController::class, 'destroySelected'])->name('tataraks.destroySelected');
        Route::resource('tataraks', TataraksController::class)
            ->except(['create', 'edit']);

        // Tambah routes untuk peminjaman di sini (mirip tataraks)
        Route::resource('peminjamans', PeminjamanController::class)
            ->except(['create', 'edit']);  // Index, show, store, update, destroy (adjust jika perlu)
        Route::post('peminjamans/return', [PeminjamanController::class, 'returnStore'])->name('peminjamans.return');
        Route::post('peminjamans/extend', [PeminjamanController::class, 'extendUpdate'])->name('peminjamans.extend');
        Route::get('peminjamans/bukus', [PeminjamanController::class, 'getBukus'])->name('peminjamans.bukus');  // Untuk modal select buku
        Route::get('peminjamans/eksemplars/{bukuId}', [PeminjamanController::class, 'getEksemplars'])->name('peminjamans.eksemplars');  // Untuk eksemplar
        Route::delete('peminjamans/destroy-selected', [PeminjamanController::class, 'destroySelected'])->name('peminjamans.destroySelected');  // Jika ada bulk delete
    });
});

// ==========================
// 📌 Admin Only
// ==========================
Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Admin-only routes (jika ada, misal approve officer atau sesuatu)
    });

require __DIR__.'/auth.php';
