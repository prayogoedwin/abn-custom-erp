<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AmbilSupplierController;
use App\Http\Controllers\BackupRestoreController;
use App\Http\Controllers\CashbonKaryawanController;
use App\Http\Controllers\CashbonKaryawanPembayaranController;
use App\Http\Controllers\CashbonSupplierController;
use App\Http\Controllers\CashbonSupplierPembayaranController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DinamisVariableController;
use App\Http\Controllers\HistoryHargaBasisController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PembelianDetailController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PengirimanDetailController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Pihak3Controller;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TitipSupplierController;
use App\Http\Controllers\UserController;
use App\Models\DinamisVariable;
use App\Models\HistoryHargaBasis;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
})->name('home');

Route::view('dashboard', 'dashboard', ['withbackup' => false])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
    Route::put('settings/appearance', [Settings\AppearanceController::class, 'update'])->name('settings.appearance.update');

    // Roles Management - dengan permission check
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:view-roles');
    Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export')->middleware('permission:download-roles');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create-roles');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create-roles');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:show-roles');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:edit-roles');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:edit-roles');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete-roles');

    // Permissions Management - dengan permission check
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:view-permissions');
    Route::get('permissions/export', [PermissionController::class, 'export'])->name('permissions.export')->middleware('permission:download-permissions');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:create-permissions');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:create-permissions');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show')->middleware('permission:show-permissions');
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:edit-permissions');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:edit-permissions');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:delete-permissions');

    // Users Management - dengan permission check
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view-users');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export')->middleware('permission:download-users');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-users');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-users');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:show-users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-users');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-users');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-users');

    // Produk Management - dengan permission check
    Route::get('produks', [ProdukController::class, 'index'])->name('produks.index')->middleware('permission:view-produks');
    Route::get('produks/export', [ProdukController::class, 'export'])->name('produks.export')->middleware('permission:download-produks');
    Route::get('produks/create', [ProdukController::class, 'create'])->name('produks.create')->middleware('permission:create-produks');
    Route::post('produks', [ProdukController::class, 'store'])->name('produks.store')->middleware('permission:create-produks');
    Route::get('produks/{produk}', [ProdukController::class, 'show'])->name('produks.show')->middleware('permission:show-produks');
    Route::get('produks/{produk}/edit', [ProdukController::class, 'edit'])->name('produks.edit')->middleware('permission:edit-produks');
    Route::put('produks/{produk}', [ProdukController::class, 'update'])->name('produks.update')->middleware('permission:edit-produks');
    Route::delete('produks/{produk}', [ProdukController::class, 'destroy'])->name('produks.destroy')->middleware('permission:delete-produks');

    // Kategori Produk Management - dengan permission check
    Route::get('kategoris', [KategoriProdukController::class, 'index'])->name('kategoris.index')->middleware('permission:view-kategoris');
    Route::get('kategoris/export', [KategoriProdukController::class, 'export'])->name('kategoris.export')->middleware('permission:download-kategoris');
    Route::get('kategoris/create', [KategoriProdukController::class, 'create'])->name('kategoris.create')->middleware('permission:create-kategoris');
    Route::post('kategoris', [KategoriProdukController::class, 'store'])->name('kategoris.store')->middleware('permission:create-kategoris');
    Route::get('kategoris/{kategori}', [KategoriProdukController::class, 'show'])->name('kategoris.show')->middleware('permission:show-kategoris');
    Route::get('kategoris/{kategori}/edit', [KategoriProdukController::class, 'edit'])->name('kategoris.edit')->middleware('permission:edit-kategoris');
    Route::put('kategoris/{kategori}', [KategoriProdukController::class, 'update'])->name('kategoris.update')->middleware('permission:edit-kategoris');
    Route::delete('kategoris/{kategori}', [KategoriProdukController::class, 'destroy'])->name('kategoris.destroy')->middleware('permission:delete-kategoris');

    // Stoks Produk Management - dengan permission check
    Route::get('stoks', [StokController::class, 'index'])->name('stoks.index')->middleware('permission:view-stoks');
    Route::get('stoks/export', [StokController::class, 'export'])->name('stoks.export')->middleware('permission:download-stoks');
    Route::get('stoks/create', [StokController::class, 'create'])->name('stoks.create')->middleware('permission:create-stoks');
    Route::post('stoks', [StokController::class, 'store'])->name('stoks.store')->middleware('permission:create-stoks');
    Route::get('stoks/{stok}', [StokController::class, 'show'])->name('stoks.show')->middleware('permission:show-stoks');
    Route::get('stoks/{stok}/edit', [StokController::class, 'edit'])->name('stoks.edit')->middleware('permission:edit-stoks');
    Route::put('stoks/{stok}', [StokController::class, 'update'])->name('stoks.update')->middleware('permission:edit-stoks');
    Route::delete('stoks/{stok}', [StokController::class, 'destroy'])->name('stoks.destroy')->middleware('permission:delete-stoks');

    // History harga Produk Management - dengan permission check
    Route::get('history_harga_bases', [HistoryHargaBasisController::class, 'index'])->name('history_harga_bases.index')->middleware('permission:view-history_harga_bases');
    Route::get('history_harga_bases/export', [HistoryHargaBasisController::class, 'export'])->name('history_harga_bases.export')->middleware('permission:download-history_harga_bases');
    Route::get('history_harga_bases/create', [HistoryHargaBasisController::class, 'create'])->name('history_harga_bases.create')->middleware('permission:create-history_harga_bases');
    Route::post('history_harga_bases', [HistoryHargaBasisController::class, 'store'])->name('history_harga_bases.store')->middleware('permission:create-history_harga_bases');
    Route::get('history_harga_bases/{historyhargabasis}', [HistoryHargaBasisController::class, 'show'])->name('history_harga_bases.show')->middleware('permission:show-history_harga_bases');
    Route::get('history_harga_bases/{historyhargabasis}/edit', [HistoryHargaBasisController::class, 'edit'])->name('history_harga_bases.edit')->middleware('permission:edit-history_harga_bases');
    Route::put('history_harga_bases/{historyhargabasis}', [HistoryHargaBasisController::class, 'update'])->name('history_harga_bases.update')->middleware('permission:edit-history_harga_bases');
    Route::delete('history_harga_bases/{historyhargabasis}', [HistoryHargaBasisController::class, 'destroy'])->name('history_harga_bases.destroy')->middleware('permission:delete-history_harga_bases');

    // Supplier Produk Management - dengan permission check
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index')->middleware('permission:view-suppliers');
    Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export')->middleware('permission:download-suppliers');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('permission:create-suppliers');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:create-suppliers');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show')->middleware('permission:show-suppliers');
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:edit-suppliers');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:edit-suppliers');
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:delete-suppliers');

    // Karyawan Produk Management - dengan permission check
    Route::get('karyawans', [KaryawanController::class, 'index'])->name('karyawans.index')->middleware('permission:view-karyawans');
    Route::get('karyawans/export', [KaryawanController::class, 'export'])->name('karyawans.export')->middleware('permission:download-karyawans');
    Route::get('karyawans/create', [KaryawanController::class, 'create'])->name('karyawans.create')->middleware('permission:create-karyawans');
    Route::post('karyawans', [KaryawanController::class, 'store'])->name('karyawans.store')->middleware('permission:create-karyawans');
    Route::get('karyawans/{karyawan}', [KaryawanController::class, 'show'])->name('karyawans.show')->middleware('permission:show-karyawans');
    Route::get('karyawans/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawans.edit')->middleware('permission:edit-karyawans');
    Route::put('karyawans/{karyawan}', [KaryawanController::class, 'update'])->name('karyawans.update')->middleware('permission:edit-karyawans');
    Route::delete('karyawans/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawans.destroy')->middleware('permission:delete-karyawans');

    // pihak3s Produk Management - dengan permission check
    Route::get('pihak3s', [Pihak3Controller::class, 'index'])->name('pihak3s.index')->middleware('permission:view-pihak3s');
    Route::get('pihak3s/export', [Pihak3Controller::class, 'export'])->name('pihak3s.export')->middleware('permission:download-pihak3s');
    Route::get('pihak3s/create', [Pihak3Controller::class, 'create'])->name('pihak3s.create')->middleware('permission:create-pihak3s');
    Route::post('pihak3s', [Pihak3Controller::class, 'store'])->name('pihak3s.store')->middleware('permission:create-pihak3s');
    Route::get('pihak3s/{pihak3}', [Pihak3Controller::class, 'show'])->name('pihak3s.show')->middleware('permission:show-pihak3s');
    Route::get('pihak3s/{pihak3}/edit', [Pihak3Controller::class, 'edit'])->name('pihak3s.edit')->middleware('permission:edit-pihak3s');
    Route::put('pihak3s/{pihak3}', [Pihak3Controller::class, 'update'])->name('pihak3s.update')->middleware('permission:edit-pihak3s');
    Route::delete('pihak3s/{pihak3}', [Pihak3Controller::class, 'destroy'])->name('pihak3s.destroy')->middleware('permission:delete-pihak3s');

    // Supplier Produk Management - dengan permission check
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:view-customers');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export')->middleware('permission:download-customers');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:create-customers');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:create-customers');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->middleware('permission:show-customers');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:edit-customers');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:edit-customers');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:delete-customers');

    // DinamisVariable Produk Management - dengan permission check
    Route::get('dinamisvariables', [DinamisVariableController::class, 'index'])->name('dinamisvariables.index')->middleware('permission:view-dinamisvariables');
    Route::get('dinamisvariables/export', [DinamisVariableController::class, 'export'])->name('dinamisvariables.export')->middleware('permission:download-dinamisvariables');
    Route::get('dinamisvariables/create', [DinamisVariableController::class, 'create'])->name('dinamisvariables.create')->middleware('permission:create-dinamisvariables');
    Route::post('dinamisvariables', [DinamisVariableController::class, 'store'])->name('dinamisvariables.store')->middleware('permission:create-dinamisvariables');
    Route::get('dinamisvariables/{dinamisvariable}', [DinamisVariableController::class, 'show'])->name('dinamisvariables.show')->middleware('permission:show-dinamisvariables');
    Route::get('dinamisvariables/{dinamisvariable}/edit', [DinamisVariableController::class, 'edit'])->name('dinamisvariables.edit')->middleware('permission:edit-dinamisvariables');
    Route::put('dinamisvariables/{dinamisvariable}', [DinamisVariableController::class, 'update'])->name('dinamisvariables.update')->middleware('permission:edit-dinamisvariables');
    Route::delete('dinamisvariables/{dinamisvariable}', [DinamisVariableController::class, 'destroy'])->name('dinamisvariables.destroy')->middleware('permission:delete-dinamisvariables');

    // Pembelians Management - dengan permission check
    Route::get('pembelians', [PembelianController::class, 'index'])->name('pembelians.index')->middleware('permission:view-pembelians');
    Route::get('pembelians/export', [PembelianController::class, 'export'])->name('pembelians.export')->middleware('permission:download-pembelians');
    Route::get('pembelians/create', [PembelianController::class, 'create'])->name('pembelians.create')->middleware('permission:create-pembelians');

    Route::get('pembelians/createlanjut/{pembelian}', [PembelianController::class, 'createlanjut'])->name('pembelians.createlanjut')->middleware('permission:create-pembelians');

    Route::post('pembelians', [PembelianController::class, 'store'])->name('pembelians.store')->middleware('permission:create-pembelians');

    //storelanjut
    Route::post('pembelians/storelanjut/{pembelian}', [PembelianController::class, 'storelanjut'])->name('pembelians.storelanjut')->middleware('permission:create-pembelians');


    Route::get('pembelians/{pembelian}', [PembelianController::class, 'show'])->name('pembelians.show')->middleware('permission:show-pembelians');
    Route::get('pembelians/{pembelian}/edit', [PembelianController::class, 'edit'])->name('pembelians.edit')->middleware('permission:edit-pembelians');
    Route::get('pembelians/{pembelian}/editlanjut', [PembelianController::class, 'editlanjut'])->name('pembelians.editlanjut')->middleware('permission:edit-pembelians');
    Route::put('pembelians/{pembelian}/update', [PembelianController::class, 'update'])->name('pembelians.update')->middleware('permission:edit-pembelians');
    Route::put('pembelians/{pembelian}/updatelanjut', [PembelianController::class, 'updatelanjut'])->name('pembelians.updatelanjut')->middleware('permission:edit-pembelians');
    Route::delete('pembelians/{pembelian}', [PembelianController::class, 'destroy'])->name('pembelians.destroy')->middleware('permission:delete-pembelians');

    Route::get('pembelians/{pembelian}/cetaknota', [PembelianController::class, 'cetakNota'])->name('pembelians.cetaknota')->middleware('permission:show-pembelians');


    // PembeliansDetails Management - dengan permission check
    Route::get('pembeliandetails', [PembelianDetailController::class, 'index'])->name('pembeliandetails.index')->middleware('permission:view-pembeliandetails');
    Route::get('pembeliandetails/export', [PembelianDetailController::class, 'export'])->name('pembeliandetails.export')->middleware('permission:download-pembeliandetails');
    Route::get('pembeliandetails/create', [PembelianDetailController::class, 'create'])->name('pembeliandetails.create')->middleware('permission:create-pembeliandetails');
    Route::get('pembeliandetails/createnow/{pembelian}', [PembelianDetailController::class, 'createnow'])->name('pembeliandetails.createnow')->middleware('permission:create-pembeliandetails');
    Route::post('pembeliandetails', [PembelianDetailController::class, 'store'])->name('pembeliandetails.store')->middleware('permission:create-pembeliandetails');


    Route::get('pembeliandetails/{pembeliandetail}', [PembelianDetailController::class, 'show'])->name('pembeliandetails.show')->middleware('permission:show-pembeliandetails');

    Route::get('pembeliandetails/{pembelian}/editnow', [PembelianDetailController::class, 'editnow'])->name('pembeliandetails.editnow')->middleware('permission:edit-pembeliandetails');

    Route::put('pembeliandetails/{pembelian}/update', [PembelianDetailController::class, 'update'])->name('pembeliandetails.update');

    Route::delete('pembeliandetails/{pembeliandetail}/destroy', [PembelianDetailController::class, 'destroy'])->name('pembeliandetails.destroy')->middleware('permission:delete-pembeliandetails');




    // absensi Management - dengan permission check
    Route::get('absensis', [AbsensiController::class, 'index'])->name('absensis.index')->middleware('permission:view-absensis');

    Route::get('absensis/absensi', [AbsensiController::class, 'absensi'])->name('absensis.absensi')->middleware('permission:view-absensis');
    Route::post('absensis/generate', [AbsensiController::class, 'generate'])->name('absensis.generate')->middleware('permission:view-absensis');

    Route::get('absensis/export', [AbsensiController::class, 'export'])->name('absensis.export')->middleware('permission:download-absensis');
    Route::get('absensis/create', [AbsensiController::class, 'create'])->name('absensis.create')->middleware('permission:create-absensis');
    Route::post('absensis', [AbsensiController::class, 'store'])->name('absensis.store')->middleware('permission:create-absensis');
    Route::get('absensis/{absensi}', [AbsensiController::class, 'show'])->name('absensis.show')->middleware('permission:show-absensis');
    Route::get('absensis/{absensi}/edit', [AbsensiController::class, 'edit'])->name('absensis.edit')->middleware('permission:edit-absensis');
    Route::put('absensis/{absensi}', [AbsensiController::class, 'update'])->name('absensis.update')->middleware('permission:edit-absensis');
    Route::delete('absensis/{absensi}', [AbsensiController::class, 'destroy'])->name('absensis.destroy')->middleware('permission:delete-absensis');

    // pengirimans Management - dengan permission check---------------------------------------------------------
    Route::get('pengirimans', [PengirimanController::class, 'index'])->name('pengirimans.index')->middleware('permission:view-pengirimans');
    Route::get('pengirimans/export', [PengirimanController::class, 'export'])->name('pengirimans.export')->middleware('permission:download-pengirimans');
    Route::get('pengirimans/create', [PengirimanController::class, 'create'])->name('pengirimans.create')->middleware('permission:create-pengirimans');

    Route::get('pengirimans/createlanjut/{pengiriman}', [PengirimanController::class, 'createlanjut'])->name('pengirimans.createlanjut')->middleware('permission:create-pengirimans');

    Route::post('pengirimans', [PengirimanController::class, 'store'])->name('pengirimans.store')->middleware('permission:create-pengirimans');

    //storelanjut
    Route::post('pengirimans/storelanjut/{pengiriman}', [PengirimanController::class, 'storelanjut'])->name('pengirimans.storelanjut')->middleware('permission:create-pengirimans');


    Route::get('pengirimans/{pengiriman}', [PengirimanController::class, 'show'])->name('pengirimans.show')->middleware('permission:show-pengirimans');
    Route::get('pengirimans/{pengiriman}/edit', [PengirimanController::class, 'edit'])->name('pengirimans.edit')->middleware('permission:edit-pengirimans');
    Route::put('pengirimans/{pengiriman}/update', [PengirimanController::class, 'update'])->name('pengirimans.update')->middleware('permission:edit-pengirimans');
    Route::delete('pengirimans/{pengiriman}', [PengirimanController::class, 'destroy'])->name('pengirimans.destroy')->middleware('permission:delete-pengirimans');

    Route::get('pengirimans/{pengiriman}/cetaknota', [PengirimanController::class, 'cetakNota'])->name('pengirimans.cetaknota')->middleware('permission:show-pengirimans');

    Route::get('pengirimandetails/create/{pengiriman}', [PengirimanDetailController::class, 'create'])->name('pengirimandetails.create')->middleware('permission:create-pengirimandetails');
    Route::Post('pengirimandetails/store', [PengirimanDetailController::class, 'store'])->name('pengirimandetails.store')->middleware('permission:create-pengirimandetails');

    Route::get('pengirimandetails/edit/{pengiriman}', [PengirimanDetailController::class, 'edit'])->name('pengirimandetails.edit')->middleware('permission:edit-pengirimandetails');
    Route::put('pengirimandetails/update', [PengirimanDetailController::class, 'update'])->name('pengirimandetails.update')->middleware('permission:edit-pengirimandetails');
    //--------------------------------------------------------------------------------------------------------

    // penjualans Management - dengan permission check---------------------------------------------------------
    Route::get('penjualans', [PenjualanController::class, 'index'])->name('penjualans.index')->middleware('permission:view-penjualans');
    Route::get('penjualans/export', [PenjualanController::class, 'export'])->name('penjualans.export')->middleware('permission:download-penjualans');
    Route::get('penjualans/create', [PenjualanController::class, 'create'])->name('penjualans.create')->middleware('permission:create-penjualans');

    Route::get('penjualans/createlanjut/{penjualan}', [PenjualanController::class, 'createlanjut'])->name('penjualans.createlanjut')->middleware('permission:create-penjualans');

    Route::post('penjualans', [PenjualanController::class, 'store'])->name('penjualans.store')->middleware('permission:create-penjualans');

    //storelanjut
    Route::post('penjualans/storelanjut/{penjualan}', [PenjualanController::class, 'storelanjut'])->name('penjualans.storelanjut')->middleware('permission:create-penjualans');


    Route::get('penjualans/{penjualan}', [PenjualanController::class, 'show'])->name('penjualans.show')->middleware('permission:show-penjualans');
    Route::get('penjualans/{penjualan}/edit', [PenjualanController::class, 'edit'])->name('penjualans.edit')->middleware('permission:edit-penjualans');
    Route::put('penjualans/{penjualan}/update', [PenjualanController::class, 'update'])->name('penjualans.update')->middleware('permission:edit-penjualans');
    Route::delete('penjualans/{penjualan}', [PenjualanController::class, 'destroy'])->name('penjualans.destroy')->middleware('permission:delete-penjualans');

    Route::get('penjualans/{penjualan}/cetaknota', [PenjualanController::class, 'cetakNota'])->name('penjualans.cetaknota')->middleware('permission:show-penjualans');

    Route::get('penjualandetails/create/{penjualan}', [PenjualanDetailController::class, 'create'])->name('penjualandetails.create')->middleware('permission:create-penjualandetails');
    Route::Post('penjualandetails/store', [PenjualanDetailController::class, 'store'])->name('penjualandetails.store')->middleware('permission:create-penjualandetails');


    Route::get('penjualandetails/{penjualan}/edit', [PenjualanDetailController::class, 'edit'])->name('penjualandetails.edit')->middleware('permission:edit-penjualandetails');
    Route::put('penjualandetails/{penjualan}/update', [PenjualanDetailController::class, 'update'])->name('penjualandetails.update')->middleware('permission:edit-penjualandetails');
    //-------------------------------------------------------------------------------------------------

    //Laporan Laporan*************************************************************************************************
    TODO:
    Route::get('laporanpembelian', [LaporanController::class, 'index'])->name('laporanpembelians.index')->middleware('permission:view-laporanpembelian');
    Route::get('laporanpengiriman', [LaporanController::class, 'index'])->name('laporanpengirimans.index')->middleware('permission:view-laporanpengiriman');
    Route::get('laporanpenjualan', [LaporanController::class, 'index'])->name('laporanpenjualans.index')->middleware('permission:view-laporanpenjualan');
    //************************************************************************************************************

    //Cashbon Karyawan==================================================================================================
    Route::get('cashbonkaryawans', [CashbonKaryawanController::class, 'index'])->name('cashbonkaryawans.index')->middleware('permission:view-cashbonkaryawans');
    Route::get('cashbonkaryawans/export', [CashbonKaryawanController::class, 'export'])->name('cashbonkaryawans.export')->middleware('permission:download-cashbonkaryawans');
    Route::get('cashbonkaryawans/create', [CashbonKaryawanController::class, 'create'])->name('cashbonkaryawans.create')->middleware('permission:create-cashbonkaryawans');
    Route::post('cashbonkaryawans', [CashbonKaryawanController::class, 'store'])->name('cashbonkaryawans.store')->middleware('permission:create-cashbonkaryawans');
    Route::get('cashbonkaryawans/{cashbonkaryawan}', [CashbonKaryawanController::class, 'show'])->name('cashbonkaryawans.show')->middleware('permission:show-cashbonkaryawans');
    Route::get('cashbonkaryawans/{cashbonkaryawan}/edit', [CashbonKaryawanController::class, 'edit'])->name('cashbonkaryawans.edit')->middleware('permission:edit-cashbonkaryawans');
    Route::put('cashbonkaryawans/{cashbonkaryawan}', [CashbonKaryawanController::class, 'update'])->name('cashbonkaryawans.update')->middleware('permission:edit-cashbonkaryawans');
    Route::delete('cashbonkaryawans/{cashbonkaryawan}', [CashbonKaryawanController::class, 'destroy'])->name('cashbonkaryawans.destroy')->middleware('permission:delete-cashbonkaryawans');


    //pembayaran
    Route::get('cashbonkaryawanpembayarans', [CashbonKaryawanPembayaranController::class, 'index'])->name('cashbonkaryawanpembayarans.index')->middleware('permission:view-cashbonkaryawanpembayarans');
    Route::get('cashbonkaryawanpembayarans/export', [CashbonKaryawanPembayaranController::class, 'export'])->name('cashbonkaryawanpembayarans.export')->middleware('permission:download-cashbonkaryawanpembayarans');
    Route::get('cashbonkaryawanpembayarans/create', [CashbonKaryawanPembayaranController::class, 'create'])->name('cashbonkaryawanpembayarans.create')->middleware('permission:create-cashbonkaryawanpembayarans');
    Route::post('cashbonkaryawanpembayarans', [CashbonKaryawanPembayaranController::class, 'store'])->name('cashbonkaryawanpembayarans.store')->middleware('permission:create-cashbonkaryawanpembayarans');
    Route::get('cashbonkaryawanpembayarans/{cashbonkaryawanpembayaran}', [CashbonKaryawanPembayaranController::class, 'show'])->name('cashbonkaryawanpembayarans.show')->middleware('permission:show-cashbonkaryawanpembayarans');
    Route::get('cashbonkaryawanpembayarans/{cashbonkaryawanpembayaran}/edit', [CashbonKaryawanPembayaranController::class, 'edit'])->name('cashbonkaryawanpembayarans.edit')->middleware('permission:edit-cashbonkaryawanpembayarans');
    Route::put('cashbonkaryawanpembayarans/{cashbonkaryawanpembayaran}', [CashbonKaryawanPembayaranController::class, 'update'])->name('cashbonkaryawanpembayarans.update')->middleware('permission:edit-cashbonkaryawanpembayarans');
    Route::delete('cashbonkaryawanpembayarans/{cashbonkaryawanpembayaran}', [CashbonKaryawanPembayaranController::class, 'destroy'])->name('cashbonkaryawanpembayarans.destroy')->middleware('permission:delete-cashbonkaryawanpembayarans');
    //=============================================================================================================

    //Cashbon supplier==================================================================================================
    Route::get('cashbonsuppliers', [CashbonSupplierController::class, 'index'])->name('cashbonsuppliers.index')->middleware('permission:view-cashbonsuppliers');
    Route::get('cashbonsuppliers/export', [CashbonSupplierController::class, 'export'])->name('cashbonsuppliers.export')->middleware('permission:download-cashbonsuppliers');
    Route::get('cashbonsuppliers/create', [CashbonSupplierController::class, 'create'])->name('cashbonsuppliers.create')->middleware('permission:create-cashbonsuppliers');
    Route::post('cashbonsuppliers', [CashbonSupplierController::class, 'store'])->name('cashbonsuppliers.store')->middleware('permission:create-cashbonsuppliers');
    Route::get('cashbonsuppliers/{cashbonsupplier}', [CashbonSupplierController::class, 'show'])->name('cashbonsuppliers.show')->middleware('permission:show-cashbonsuppliers');
    Route::get('cashbonsuppliers/{cashbonsupplier}/edit', [CashbonSupplierController::class, 'edit'])->name('cashbonsuppliers.edit')->middleware('permission:edit-cashbonsuppliers');
    Route::put('cashbonsuppliers/{cashbonsupplier}', [CashbonSupplierController::class, 'update'])->name('cashbonsuppliers.update')->middleware('permission:edit-cashbonsuppliers');
    Route::delete('cashbonsuppliers/{cashbonsupplier}', [CashbonSupplierController::class, 'destroy'])->name('cashbonsuppliers.destroy')->middleware('permission:delete-cashbonsuppliers');

    //pembayaran
    Route::get('cashbonsupplierpembayarans', [CashbonSupplierPembayaranController::class, 'index'])->name('cashbonsupplierpembayarans.index')->middleware('permission:view-cashbonsupplierpembayarans');
    Route::get('cashbonsupplierpembayarans/export', [CashbonSupplierPembayaranController::class, 'export'])->name('cashbonsupplierpembayarans.export')->middleware('permission:download-cashbonsupplierpembayarans');
    Route::get('cashbonsupplierpembayarans/create', [CashbonSupplierPembayaranController::class, 'create'])->name('cashbonsupplierpembayarans.create')->middleware('permission:create-cashbonsupplierpembayarans');
    Route::post('cashbonsupplierpembayarans', [CashbonSupplierPembayaranController::class, 'store'])->name('cashbonsupplierpembayarans.store')->middleware('permission:create-cashbonsupplierpembayarans');
    Route::get('cashbonsupplierpembayarans/{cashbonsupplierpembayaran}', [CashbonSupplierPembayaranController::class, 'show'])->name('cashbonsupplierpembayarans.show')->middleware('permission:show-cashbonsupplierpembayarans');
    Route::get('cashbonsupplierpembayarans/{cashbonsupplierpembayaran}/edit', [CashbonSupplierPembayaranController::class, 'edit'])->name('cashbonsupplierpembayarans.edit')->middleware('permission:edit-cashbonsupplierpembayarans');
    Route::put('cashbonsupplierpembayarans/{cashbonsupplierpembayaran}', [CashbonSupplierPembayaranController::class, 'update'])->name('cashbonsupplierpembayarans.update')->middleware('permission:edit-cashbonsupplierpembayarans');
    Route::delete('cashbonsupplierpembayarans/{cashbonsupplierpembayaran}', [CashbonSupplierPembayaranController::class, 'destroy'])->name('cashbonsupplierpembayarans.destroy')->middleware('permission:delete-cashbonsupplierpembayarans');
    //=============================================================================================================


    //Titipan supplier==================================================================================================
    Route::get('titipsuppliers', [TitipSupplierController::class, 'index'])->name('titipsuppliers.index')->middleware('permission:view-titipsuppliers');
    Route::get('titipsuppliers/export', [TitipSupplierController::class, 'export'])->name('titipsuppliers.export')->middleware('permission:download-titipsuppliers');
    Route::get('titipsuppliers/create', [TitipSupplierController::class, 'create'])->name('titipsuppliers.create')->middleware('permission:create-titipsuppliers');
    Route::post('titipsuppliers', [TitipSupplierController::class, 'store'])->name('titipsuppliers.store')->middleware('permission:create-titipsuppliers');
    Route::get('titipsuppliers/{titipsupplier}', [TitipSupplierController::class, 'show'])->name('titipsuppliers.show')->middleware('permission:show-titipsuppliers');
    Route::get('titipsuppliers/{titipsupplier}/edit', [TitipSupplierController::class, 'edit'])->name('titipsuppliers.edit')->middleware('permission:edit-titipsuppliers');
    Route::put('titipsuppliers/{titipsupplier}', [TitipSupplierController::class, 'update'])->name('titipsuppliers.update')->middleware('permission:edit-titipsuppliers');
    Route::delete('titipsuppliers/{titipsupplier}', [TitipSupplierController::class, 'destroy'])->name('titipsuppliers.destroy')->middleware('permission:delete-titipsuppliers');

    //Ambil Supplier==================================================================================================
    Route::get('ambilsuppliers', [AmbilSupplierController::class, 'index'])->name('ambilsuppliers.index')->middleware('permission:view-ambilsuppliers');
    Route::get('ambilsuppliers/export', [AmbilSupplierController::class, 'export'])->name('ambilsuppliers.export')->middleware('permission:download-ambilsuppliers');
    Route::get('ambilsuppliers/create', [AmbilSupplierController::class, 'create'])->name('ambilsuppliers.create')->middleware('permission:create-ambilsuppliers');
    Route::post('ambilsuppliers', [AmbilSupplierController::class, 'store'])->name('ambilsuppliers.store')->middleware('permission:create-ambilsuppliers');
    Route::get('ambilsuppliers/{ambilsupplier}', [AmbilSupplierController::class, 'show'])->name('ambilsuppliers.show')->middleware('permission:show-ambilsuppliers');
    Route::get('ambilsuppliers/{ambilsupplier}/edit', [AmbilSupplierController::class, 'edit'])->name('ambilsuppliers.edit')->middleware('permission:edit-ambilsuppliers');
    Route::put('ambilsuppliers/{ambilsupplier}', [AmbilSupplierController::class, 'update'])->name('ambilsuppliers.update')->middleware('permission:edit-ambilsuppliers');
    Route::delete('ambilsuppliers/{ambilsupplier}', [AmbilSupplierController::class, 'destroy'])->name('ambilsuppliers.destroy')->middleware('permission:delete-ambilsuppliers');



    Route::get('backup', [BackupRestoreController::class, 'backup'])->name('backup.backup')->middleware('permission:delete-users'); // TODO: Ganti permission 
    Route::post('restore', [BackupRestoreController::class, 'restore'])->name('backup.restore')->middleware('permission:delete-users');
});

require __DIR__ . '/auth.php';
