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
        $companyCount = Company::count();
        $employeeCount = Employee::count();
        $userCount = User::count();

        // Son İşlemler
        $recentActivities = ActivityLog::latest()->take(10)->get();

        // Uyarılar (Basit Uyarılar)
        $alertCount = 0; // Örnek, gerçek uyarı mekanizmanıza bağlı olarak güncelleyin

        return view('dashboard', compact('companyCount', 'employeeCount', 'userCount', 'recentActivities', 'alertCount'));
    }
}
