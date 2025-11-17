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
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\MemberRegistrationController;
use App\Http\Controllers\Admin\RegistrationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================
// 📌 PUBLIC REGISTRATION ROUTES
// ==========================
Route::prefix('register')->name('registration.')->group(function () {
    Route::get('/', [MemberRegistrationController::class, 'create'])->name('create');
    Route::post('/', [MemberRegistrationController::class, 'store'])->name('store');
    Route::get('/success', [MemberRegistrationController::class, 'success'])->name('success');
    Route::get('/verify/{token}', [MemberRegistrationController::class, 'verify'])->name('verify');
    Route::get('/verified', [MemberRegistrationController::class, 'verified'])->name('verified');
    Route::post('/check-status', [MemberRegistrationController::class, 'checkStatus'])->name('checkStatus');
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

    // Resources (read-only)
    Route::resource('bukus', BukuController::class)->only(['index','show']);
    Route::resource('bukuitems', BukuItemController::class)->only(['index','show']);
    Route::resource('kategoris', KategoriController::class)->only(['index','show']);
    Route::resource('sub_kategoris', SubKategoriController::class)->only(['index','show']);
    Route::resource('raks', RakController::class)->only(['index','show']);
    Route::resource('lokasis', LokasiRakController::class)->only(['index','show']);
    Route::resource('penerbits', PenerbitController::class)->only(['index','show']);


    Route::prefix('my-peminjamans')->name('peminjamans.')->group(function () {
        Route::get('/', [PeminjamanController::class, 'myIndex'])->name('myIndex');
        Route::get('/{id}', [PeminjamanController::class, 'myShow'])->name('myShow');
    });
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

    Route::prefix('admin')->name('admin.')->group(function () {

        // =============================
        // USER MANAGEMENT
        // =============================
        Route::get('/users/online-status', [AdminController::class, 'onlineStatus'])->name('users.onlineStatus');
        Route::delete('/users/destroy-selected', [AdminController::class, 'destroySelected'])->name('users.destroySelected');
        Route::get('/users/{user}', [AdminController::class, 'show'])->name('users.show');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');

        // =============================
        // TATARAKS
        // =============================
        Route::get('tataraks/search-buku-datatable', [TataraksController::class, 'searchBukuDatatable'])->name('tataraks.searchBukuDatatable');
        Route::get('tataraks/available-items', [TataraksController::class, 'availableItems'])->name('tataraks.available-items');
        Route::get('tataraks/available-eksemplar/{id_buku}', [TataraksController::class, 'availableEksemplarByBuku'])->name('tataraks.availableEksemplarByBuku');
        Route::get('tataraks/buku-kategori/{id_buku}', [TataraksController::class, 'getBukuKategori'])->name('tataraks.getBukuKategori');
        Route::get('tataraks/rak-by-kategori', [TataraksController::class, 'getRakByKategori'])->name('tataraks.getRakByKategori');
        Route::post('tataraks/bulk', [TataraksController::class, 'bulkStore'])->name('tataraks.bulkStore');
        Route::delete('tataraks/destroy-selected', [TataraksController::class, 'destroySelected'])->name('tataraks.destroySelected');
        Route::resource('tataraks', TataraksController::class)->except(['create', 'edit']);

        // =============================
        // PEMINJAMAN - ✅ ALL SPECIFIC ROUTES BEFORE {id}
        // =============================
        // DataTable endpoints
        Route::get('peminjamans/search-member-datatable', [PeminjamanController::class, 'searchMemberDatatable'])->name('peminjamans.searchMemberDatatable');
        Route::get('peminjamans/search-buku-datatable', [PeminjamanController::class, 'searchBukuDatatable'])->name('peminjamans.searchBukuDatatable');

        // Eligibility check
        Route::get('peminjamans/check-member-eligibility/{memberId}', [PeminjamanController::class, 'checkMemberEligibility'])->name('peminjamans.checkMemberEligibility');

        // Available items
        Route::get('peminjamans/available-eksemplar/{id_buku}', [PeminjamanController::class, 'availableEksemplarByBuku'])->name('peminjamans.availableEksemplarByBuku');

        // Actions
        Route::post('peminjamans/return', [PeminjamanController::class, 'returnStore'])->name('peminjamans.return');
        Route::post('peminjamans/extend', [PeminjamanController::class, 'extendUpdate'])->name('peminjamans.extend');
        Route::delete('peminjamans/destroy-selected', [PeminjamanController::class, 'destroySelected'])->name('peminjamans.destroySelected');

        // ✅ LIST ROUTE (index)
        Route::get('peminjamans', [PeminjamanController::class, 'index'])->name('peminjamans.index');

        // ✅ CREATE ROUTE
        Route::post('peminjamans', [PeminjamanController::class, 'store'])->name('peminjamans.store');

        // ✅ SHOW ROUTE - MUST BE LAST!
        Route::get('peminjamans/{id}', [PeminjamanController::class, 'show'])
            ->name('peminjamans.show');

        // =============================
        // REGISTRATION MANAGEMENT
        // =============================
        Route::prefix('registrations')->name('registrations.')->group(function () {
            Route::post('bulk-approve', [RegistrationController::class, 'bulkApprove'])->name('bulkApprove');
            Route::post('bulk-reject', [RegistrationController::class, 'bulkReject'])->name('bulkReject');
            Route::get('/', [RegistrationController::class, 'index'])->name('index');
            Route::get('{registration}', [RegistrationController::class, 'show'])->name('show');
            Route::post('{registration}/review', [RegistrationController::class, 'review'])->name('review');
            Route::post('{registration}/approve', [RegistrationController::class, 'approve'])->name('approve');
            Route::post('{registration}/reject', [RegistrationController::class, 'reject'])->name('reject');
        });
    });
});

// ==========================
// 📌 Admin Only
// ==========================
Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Admin-only routes (jika ada)
    });

require __DIR__.'/auth.php';
