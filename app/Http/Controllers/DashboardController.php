<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $companyCount = Company::count();
            $employeeCount = Employee::count();
            $userCount = User::count();

            $recentActivitiesQuery = ActivityLog::latest();

            if (!auth()->user()->isAdmin()) {
                $companyId = auth()->user()->company_id;

                $employeeIds = Employee::where('company_id', $companyId)
                                        ->withTrashed() // Include soft-deleted employees
                                        ->pluck('id');

                $recentActivitiesQuery->where(function($query) use ($companyId, $employeeIds) {
                    $query->where('model_type', 'Company')
                          ->where('model_id', $companyId)
                          ->orWhere(function($query) use ($employeeIds) {
                              $query->where('model_type', 'Employee')
                                    ->whereIn('model_id', $employeeIds);
                          });
                });
            }

            $recentActivities = $recentActivitiesQuery->take(10)->get();
            $alertCount = 0; 

            return view('dashboard', compact('companyCount', 'employeeCount', 'userCount', 'recentActivities', 'alertCount'));
        } else {
            return redirect()->route('login'); 
        }
    }
}
