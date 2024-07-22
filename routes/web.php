<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LogoutController;

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search routes
Route::get('/companies/search', [CompanyController::class, 'search'])->name('companies.search');
Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');

// Middleware group for authenticated users
Route::middleware('auth')->group(function () {
    // Admin-only routes (ensure admin middleware is properly defined)
    Route::middleware('admin')->group(function () {
        Route::resource('companies', CompanyController::class);
        Route::resource('employees', EmployeeController::class);
    });

    // Allow employees to view companies and employees but not modify
    Route::resource('companies', CompanyController::class)->only(['index', 'show']);
    Route::resource('employees', EmployeeController::class)->only(['index', 'show']);
});

// Authentication routes
Auth::routes(['register' => false]);

// Logout route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
