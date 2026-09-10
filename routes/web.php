<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'tenant', 'tenant.permissions'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Organização
    Route::get('/settings/organization', [TenantSettingsController::class, 'edit'])->name('tenant.settings.edit');
    Route::put('/settings/organization', [TenantSettingsController::class, 'update'])->name('tenant.settings.update');

    // Permissões
    Route::get('permissions/index', [PermissionController::class, 'index'])->name('tenant.permissions.index');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('tenant.permissions.create');
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('tenant.permissions.edit');
    Route::post('permissions/store', [PermissionController::class, 'store'])->name('tenant.permissions.store');
    Route::put('permissions/update/{permission}', [PermissionController::class, 'update'])->name('tenant.permissions.update');
    Route::delete('permissions/destroy', [PermissionController::class, 'destroy'])->name('tenant.permissions.destroy');

    // Perfil
    // Route::get('roles/index', [RoleController::class, 'index'])->name('tenant.roles.index');
    // Route::get('roles/create', [RoleController::class, 'create'])->name('tenant.roles.create');
    // Route::get('roles/{role}', [RoleController::class, 'show'])->name('tenant.roles.show');
    // Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('tenant.roles.edit');
    // Route::post('roles/store', [RoleController::class, 'store'])->name('tenant.roles.store');
    // Route::put('roles/update/{role}', [RoleController::class, 'update'])->name('tenant.roles.update');
    // Route::delete('roles/destroy', [RoleController::class, 'destroy'])->name('tenant.roles.destroy');

    Route::resource('roles', RoleController::class)->names([
        'index'   => 'tenant.roles.index',
        'create'  => 'tenant.roles.create',
        'store'   => 'tenant.roles.store',
        'show'    => 'tenant.roles.show',
        'edit'    => 'tenant.roles.edit',
        'update'  => 'tenant.roles.update',
        'destroy' => 'tenant.roles.destroy',
    ]);

});

require __DIR__.'/auth.php';
