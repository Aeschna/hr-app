<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountController;

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search routes
Route::get('/companies/search', [CompanyController::class, 'search'])->name('companies.search');
Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');

// Middleware group for authenticated users
Route::middleware('auth')->group(function() {
    // Admin-only routes (ensure admin middleware is properly defined)
    Route::middleware('admin')->group(function() {
        Route::resource('companies', CompanyController::class);
        Route::resource('employees', EmployeeController::class);
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    });

    // User only routes
    Route::resource('companies', CompanyController::class)->only(['index', 'show']);
    Route::resource('employees', EmployeeController::class)->only(['index', 'show']);
    
    // Form creation route (not typically resourceful, ensure it's needed)
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
});

// Authentication routes
Auth::routes(['register' => false]);

// Logout route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Soft Delete Restore routes
Route::put('companies/{id}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
Route::put('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

// Force delete
Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.forceDelete');
Route::delete('/companies/{id}/force-delete', [CompanyController::class, 'forceDelete'])->name('companies.forceDelete');

// My Account route
Route::middleware('auth')->get('/my-account', [AccountController::class, 'index'])->name('my-account');
// My Account update route
Route::middleware('auth')->put('/my-account', [AccountController::class, 'update'])->name('account.update');
