<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LogoutController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/companies/search', [CompanyController::class, 'search'])->name('companies.search');
Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');


Route::resource('companies', CompanyController::class)->middleware('auth');
Route::resource('employees', EmployeeController::class)->middleware('auth');




Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Auth::routes(['register' => false]);

