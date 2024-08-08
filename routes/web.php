<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
// Home route
//Route::get('/', [HomeController::class, 'index'])->name('home');

// Search routes
Route::get('/companies/search', [CompanyController::class, 'search'])->name('companies.search');
Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');

// Middleware group for authenticated users
Route::middleware('auth')->group(function() {
    // Admin-only routes (ensure admin middleware is properly defined)
    Route::middleware('admin')->group(function() {
        Route::resource('companies', CompanyController::class);
        
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
       // Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');

       // Form creation route (not typically resourceful, ensure it's needed)
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
   
    //User Page Admin Only Routes
    // routes/web.php

    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');





});
Route::resource('employees', EmployeeController::class);
    // User-only routes
    //Route::resource('companies', CompanyController::class)->only(['index', 'show']);
    Route::resource('employees', EmployeeController::class)->only(['index', 'show']);
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    
   //User Page Route
   Route::get('/users', [UserController::class, 'index'])->name('users.index'); 
    
    // My Account route
    Route::get('/my-account', [AccountController::class, 'index'])->name('my-account');
    // My Account update route
    Route::put('/my-account', [AccountController::class, 'update'])->name('account.update');
    
    // Change Password Routes
    Route::get('/password/change', [AccountController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/password/change', [AccountController::class, 'changePassword'])->name('password.update');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

// Authentication routes
Auth::routes(['register' => false]);

// Logout route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Soft Delete Restore routes
Route::put('companies/{id}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
Route::put('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
Route::put('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

// Force delete
Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.forceDelete');
Route::delete('/companies/{id}/force-delete', [CompanyController::class, 'forceDelete'])->name('companies.forceDelete');
Route::delete('/users/{id}/force-delete', [UserController::class, 'forcedelete'])->name('users.forceDelete');

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
