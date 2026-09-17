<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductLotController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
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

    //Permiss[oes]
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permission.view')->name('tenant.permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->middleware('permission:permission.create')->name('tenant.permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permission.create')->name('tenant.permissions.store');
    Route::get('/permissions/{role}', [PermissionController::class, 'show'])->middleware('permission:permission.view')->name('tenant.permissions.show');
    Route::get('/permissions/{role}/edit', [PermissionController::class, 'edit'])->middleware('permission:permission.edit')->name('tenant.permissions.edit');
    Route::put('/permissions/{role}', [PermissionController::class, 'update'])->middleware('permission:permission.edit')->name('tenant.permissions.update');
    Route::delete('/permissions/{role}', [PermissionController::class, 'destroy'])->middleware('permission:permission.destroy')->name('tenant.permissions.destroy');

    // Cliente
    Route::get('/clients', [ClientController::class, 'index'])->middleware('permission:client.view')->name('tenant.clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->middleware('permission:client.create')->name('tenant.clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->middleware('permission:client.create')->name('tenant.clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->middleware('permission:client.view')->name('tenant.clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->middleware('permission:client.edit')->name('tenant.clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->middleware('permission:client.edit')->name('tenant.clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->middleware('permission:client.destroy')->name('tenant.clients.destroy');

    // POS
    Route::get('/products/search',[SaleController::class, 'search'])->name('tenant.sales.products');
    Route::resource('sales', SaleController::class)->except(['edit', 'update'])->names('tenant.sales');
    Route::post('/{sale}/cancel',[SaleController::class, 'cancel'])->name('tenant.sales.cancel');
    Route::get('/pos', [PosController::class, 'index'])->name('tenant.pos.index');
    Route::get('/pos/products/{id}',[POSController::class, 'product'])->name('pos.products.show');
    Route::post('/pos/checkout',[POSController::class, 'checkout'])->name('pos.checkout');
    //Route::get('/pos/products/search',[POSController::class, 'searchProducts'])->name('pos.products.search');
    
    Route::resource('invoices', InvoiceController::class)->names('tenant.invoices');
    Route::get('/invoices/{sale}/invoice', [InvoiceController::class, 'invoice'])->name('tenant.invoices.invoice');
    Route::post('/invoices/{sale}/cancel', [InvoiceController::class, 'cancel'])->name('tenant.invoices.cancel');
    Route::get('/invoices/products/search', [InvoiceController::class, 'products'])->name('tenant.invoices.products');

    // produtod
    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:product.view')->name('tenant.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('permission:product.create')->name('tenant.products.create');
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:product.create')->name('tenant.products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('permission:product.view')->name('tenant.products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:product.edit')->name('tenant.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:product.edit')->name('tenant.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:product.destroy')->name('tenant.products.destroy');
    
    // Lotes
    Route::resource('product-lots', ProductLotController::class)->names('tenant.product-lots');
    // Movimentos de estoque
    Route::resource('stock-movements', StockMovementController::class)->except(['edit', 'update',])->names('tenant.stock-movements');
    Route::get('stock-movements/product/{product}/lots', [StockMovementController::class, 'productLots'])->name('tenant.stock-movements.product-lots');
    //vendas
 
    //Perfil
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:role.view')->name('tenant.roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:role.create')->name('tenant.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:role.create')->name('tenant.roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:role.view')->name('tenant.roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:role.edit')->name('tenant.roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:role.edit')->name('tenant.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:role.destroy')->name('tenant.roles.destroy');

    //utilizadores
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:user.view')->name('tenant.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:user.create')->name('tenant.users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:user.create')->name('tenant.users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:user.view')->name('tenant.users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:user.edit')->name('tenant.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:user.edit')->name('tenant.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:user.destroy')->name('tenant.users.destroy');

    Route::get('/reception/index', [ReceptionController::class, 'index'])->name('tenant.reception.index');
    Route::get('/reception/create', [ReceptionController::class, 'create'])->name('tenant.reception.create');
    Route::post('/reception/store', [ReceptionController::class, 'store'])->name('tenant.reception.store');
    Route::get('/reception/{appointment}', [ReceptionController::class, 'show'])->name('tenant.reception.show');
    Route::get('/reception/{appointment}/edit', [ReceptionController::class, 'edit'])->name('tenant.reception.edit');
    Route::put('/reception/{appointment}', [ReceptionController::class, 'update'])->name('tenant.reception.update');
    Route::delete('/reception/{appointment}', [ReceptionController::class, 'destroy'])->name('tenant.reception.destroy');

    Route::get('/reception/{appointment}/triage', [ReceptionController::class, 'triage'])->name('tenant.reception.triage');
    Route::post('/reception/{appointment}/triage', [ReceptionController::class, 'saveTriage'])->name('tenant.reception.triage.store');
    Route::post('/reception/{appointment}/services', [ReceptionController::class, 'saveServices'])->name('tenant.reception.services.store');
    Route::get('/reception/{appointment}/payment', [ReceptionController::class, 'payment'])->name('tenant.reception.payment');
    Route::post('/reception/{appointment}/payment', [ReceptionController::class, 'savePayment'])->name('tenant.reception.payment.store');

    Route::get('/reception/{appointment}/ficha', [ReceptionController::class, 'ficha'])->name('tenant.reception.ficha');
    Route::post('/reception/{appointment}/refer', [ReceptionController::class, 'refer'])->name('tenant.reception.refer');
    Route::post('/reception/{appointment}/start', [ReceptionController::class, 'start'])->name('tenant.reception.start');
    Route::post('/reception/{appointment}/cancel', [ReceptionController::class, 'cancel'])->name('tenant.reception.cancel');

});


require __DIR__.'/auth.php';
