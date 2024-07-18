<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LogoutController;

Route::get('/', function () {
    return view('welcome');
});

// Şirketler için CRUD rotaları
Route::resource('companies', CompanyController::class)->middleware('auth');

// Çalışanlar için CRUD rotaları
Route::resource('employees', EmployeeController::class)->middleware('auth');

Auth::routes(['register' => false]);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

