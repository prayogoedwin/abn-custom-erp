<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Pihak3Controller;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
})->name('home');

Route::view('dashboard', 'dashboard')
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

    // Supplier Produk Management - dengan permission check
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index')->middleware('permission:view-suppliers');
    Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export')->middleware('permission:download-kategoris');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('permission:create-suppliers');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:create-suppliers'); 
    Route::get('suppliers/{kategori}', [SupplierController::class, 'show'])->name('suppliers.show')->middleware('permission:show-suppliers');
    Route::get('suppliers/{kategori}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:edit-suppliers');
    Route::put('suppliers/{kategori}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:edit-suppliers');
    Route::delete('suppliers/{kategori}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:delete-suppliers');

    // Karyawan Produk Management - dengan permission check
    Route::get('karyawans', [KaryawanController::class, 'index'])->name('karyawans.index')->middleware('permission:view-karyawans');
    Route::get('karyawans/export', [KaryawanController::class, 'export'])->name('karyawans.export')->middleware('permission:download-kategoris');
    Route::get('karyawans/create', [KaryawanController::class, 'create'])->name('karyawans.create')->middleware('permission:create-karyawans');
    Route::post('karyawans', [KaryawanController::class, 'store'])->name('karyawans.store')->middleware('permission:create-karyawans'); 
    Route::get('karyawans/{kategori}', [KaryawanController::class, 'show'])->name('karyawans.show')->middleware('permission:show-karyawans');
    Route::get('karyawans/{kategori}/edit', [KaryawanController::class, 'edit'])->name('karyawans.edit')->middleware('permission:edit-karyawans');
    Route::put('karyawans/{kategori}', [KaryawanController::class, 'update'])->name('karyawans.update')->middleware('permission:edit-karyawans');
    Route::delete('karyawans/{kategori}', [KaryawanController::class, 'destroy'])->name('karyawans.destroy')->middleware('permission:delete-karyawans');

    // pihak3s Produk Management - dengan permission check
    Route::get('pihak3s', [Pihak3Controller::class, 'index'])->name('pihak3s.index')->middleware('permission:view-pihak3s');
    Route::get('pihak3s/export', [Pihak3Controller::class, 'export'])->name('pihak3s.export')->middleware('permission:download-kategoris');
    Route::get('pihak3s/create', [Pihak3Controller::class, 'create'])->name('pihak3s.create')->middleware('permission:create-pihak3s');
    Route::post('pihak3s', [Pihak3Controller::class, 'store'])->name('pihak3s.store')->middleware('permission:create-pihak3s'); 
    Route::get('pihak3s/{kategori}', [Pihak3Controller::class, 'show'])->name('pihak3s.show')->middleware('permission:show-pihak3s');
    Route::get('pihak3s/{kategori}/edit', [Pihak3Controller::class, 'edit'])->name('pihak3s.edit')->middleware('permission:edit-pihak3s');
    Route::put('pihak3s/{kategori}', [Pihak3Controller::class, 'update'])->name('pihak3s.update')->middleware('permission:edit-pihak3s');
    Route::delete('pihak3s/{kategori}', [Pihak3Controller::class, 'destroy'])->name('pihak3s.destroy')->middleware('permission:delete-pihak3s');

    // Supplier Produk Management - dengan permission check
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:view-customers');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export')->middleware('permission:download-kategoris');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:create-customers');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:create-customers'); 
    Route::get('customers/{kategori}', [CustomerController::class, 'show'])->name('customers.show')->middleware('permission:show-customers');
    Route::get('customers/{kategori}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:edit-customers');
    Route::put('customers/{kategori}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:edit-customers');
    Route::delete('customers/{kategori}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:delete-customers');
});

require __DIR__.'/auth.php';
