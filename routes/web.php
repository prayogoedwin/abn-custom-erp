<?php

use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings;
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
    Route::get('kategoris', [KategoriProdukController::class, 'index'])->name('kategoris.index');
    Route::get('kategoris/export', [KategoriProdukController::class, 'export'])->name('kategoris.export')->middleware('permission:download-kategoris');
    Route::get('kategoris/create', [KategoriProdukController::class, 'create'])->name('kategoris.create')->middleware('permission:create-kategoris');
    Route::post('kategoris', [KategoriProdukController::class, 'store'])->name('kategoris.store')->middleware('permission:create-kategoris'); 
    Route::get('kategoris/{kategori}', [KategoriProdukController::class, 'show'])->name('kategoris.show')->middleware('permission:show-kategoris');
    Route::get('kategoris/{kategori}/edit', [KategoriProdukController::class, 'edit'])->name('kategoris.edit')->middleware('permission:edit-kategoris');
    Route::put('kategoris/{kategori}', [KategoriProdukController::class, 'update'])->name('kategoris.update')->middleware('permission:edit-kategoris');
    Route::delete('kategoris/{kategori}', [KategoriProdukController::class, 'destroy'])->name('kategoris.destroy')->middleware('permission:delete-kategoris');
});

require __DIR__.'/auth.php';
