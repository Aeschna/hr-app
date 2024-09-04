<?php

use App\Http\Controllers\Api\CompanyApiController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;

//Route::post('/login', [LoginController::class, 'login']);
// API routes
Route::middleware('auth:api')->prefix('companies')->group(function () {
    Route::get('/', [CompanyApiController::class, 'index']);
    Route::post('/', [CompanyApiController::class, 'store']);
    Route::get('/{id}', [CompanyApiController::class, 'show']);
    Route::put('/{id}', [CompanyApiController::class, 'update']);
    Route::delete('/{id}', [CompanyApiController::class, 'destroy']);
    Route::post('/{id}/restore', [CompanyApiController::class, 'restore']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);
    Route::put('/employees/{employee}/restore', [EmployeeController::class, 'restore']);
    Route::delete('/employees/{employee}/force', [EmployeeController::class, 'forceDelete']);
    Route::get('/user', [UserController::class, 'getAuthenticatedUser']);
});
