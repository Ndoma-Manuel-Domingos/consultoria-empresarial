<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TenantSettingsController;
use App\Http\Controllers\UserController;
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
    Route::resource('roles', RoleController::class)->names([
        'index'   => 'tenant.roles.index',
        'create'  => 'tenant.roles.create',
        'store'   => 'tenant.roles.store',
        'show'    => 'tenant.roles.show',
        'edit'    => 'tenant.roles.edit',
        'update'  => 'tenant.roles.update',
        'destroy' => 'tenant.roles.destroy',
    ]);

        // Listar utilizadores
    Route::get('/users', [UserController::class, 'index'])
        ->name('tenant.users.index');

    // User
    Route::get('/users/create', [UserController::class, 'create'])->name('tenant.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('tenant.users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('tenant.users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('tenant.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('tenant.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('tenant.users.destroy');

});

require __DIR__.'/auth.php';
