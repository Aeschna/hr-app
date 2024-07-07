<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

// Şirketler için CRUD rotaları
Route::resource('companies', CompanyController::class);

// Çalışanlar için CRUD rotaları
Route::resource('employees', EmployeeController::class);
