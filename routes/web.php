<?php

declare(strict_types=1);

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/', [ReportController::class, 'store'])->name('store')->middleware('throttle:3,1');
        Route::post('/upload', [ReportController::class, 'upload'])->name('upload')->middleware('throttle:10,1');
        
        Route::patch('/{report}/approve', [ReportController::class, 'approve'])->name('approve');
        Route::patch('/{report}/reject', [ReportController::class, 'reject'])->name('reject');
        
        Route::get('/{report}/status', [ReportController::class, 'checkStatus'])->name('status');
        Route::get('/{report}/download', [ReportController::class, 'download'])
            ->name('download')
            ->middleware(['signed', 'throttle:10,1']);
    });

    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [CompanyController::class, 'index'])->name('dashboard');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::post('/managers', [CompanyController::class, 'storeManager'])->name('managers.store');
        Route::put('/managers/{user}', [CompanyController::class, 'updateManager'])->name('managers.update');
        Route::delete('/managers/{user}', [CompanyController::class, 'destroyManager'])->name('managers.destroy');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('manager.clients.index');
        })->name('dashboard');

        Route::resource('clients', ClientController::class)->except(['show']);

        Route::get('/financial-audits', [ReportController::class, 'financialIndex'])->name('financial.index');
        Route::post('/financial-audits', [ReportController::class, 'financialStore'])
            ->name('financial.store')
            ->middleware('throttle:3,1');
    });

    Route::middleware('role:client')->prefix('client')->name('client.')->group(function () {
        Route::get('/portal', [PortalController::class, 'index'])->name('portal');
    });
});