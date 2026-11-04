<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\ReportController;

// 🌐 Home Page
Route::get('/', function () {
    return view('welcome');
});

// 🛡️ Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AssetController::class, 'index'])->name('dashboard');

    // 📄 PDF Report Routes 
    Route::get('/reports/stock-summary', [ReportController::class, 'stockSummaryPdf'])->name('reports.stock-summary');
    Route::get('/reports/movement', [ReportController::class, 'movementReportPdf'])->name('reports.movement');

    // Admin Only User Management & Staff Registration
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [RegisteredUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [RegisteredUserController::class, 'store'])->name('admin.users.store'); 
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

   // Asset Routes (Static / Specific routes first)
    Route::post('/assets/scan', [AssetController::class, 'scan'])->name('assets.scan');
    Route::get('/assets-export', [AssetController::class, 'export'])->name('assets.export');

  
    Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import');
    Route::get('/assets/download-sample-csv', [AssetController::class, 'downloadSampleCsv'])->name('assets.sample-csv');

    Route::get('/assets/sku/{sku}', [AssetController::class, 'bySku'])->name('assets.by-sku');

    // Asset CRUD & Dynamic Parameter Routes (Below static routes)
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // Asset Actions & Maintenance Routes
    Route::post('/assets/{asset}/transfer', [AssetTransferController::class, 'store'])->name('assets.transfer');
    Route::get('/assets/{asset}/maintenances', [AssetMaintenanceController::class, 'index'])->name('assets.maintenances.index');
    Route::post('/assets/{asset}/maintenances', [AssetMaintenanceController::class, 'store'])->name('assets.maintenances.store');
    Route::delete('/assets/maintenances/{id}', [AssetMaintenanceController::class, 'destroy'])->name('assets.maintenances.destroy');
    Route::put('/assets/maintenances/{id}', [AssetMaintenanceController::class, 'update'])->name('assets.maintenances.update');
    
    // Activity Logs
    Route::get('/activity-logs', [AssetController::class, 'activityLogs'])->name('activity.logs')->middleware('auth');


    // Stock Transactions
    Route::post('/assets/{asset}/transactions', [StockTransactionController::class, 'store'])->name('stock.store');

    //Assign
    Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('/asset-items/{item}/assign', [App\Http\Controllers\AssetController::class, 'assignItem'])->name('asset-items.assign');

    // Category Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit'); 
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update'); 
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Warehouse Routes
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🛑 Public Register ကို ပိတ်ခြင်း
Route::get('/register', function () {
    abort(404);
});
Route::post('/register', function () {
    abort(404);
});

// 🔑 Login နှင့် Password Reset Routes များ
require __DIR__.'/auth.php';