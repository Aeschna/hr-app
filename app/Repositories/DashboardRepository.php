<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;

class DashboardRepository
{
    public function getDashboardData(): array
    {
        $data = [
            'companyCount'  => Company::query()->count(),
            'employeeCount' => Employee::query()->count(),
            'userCount'     => User::query()->count(),
            'alertCount'    => 0,
        ];

        if (auth()->user()->isAdmin()) {
            $data['recentActivities']  = ActivityLog::query()->latest()->take(10)->get();
            $data['userEmployeeCount'] = 0;

            return $data;
        }

        $companyId = auth()->user()->company_id;

        $employeeIds = Employee::where('company_id', $companyId)
            ->withTrashed() // Include soft-deleted employees
            ->pluck('id');

        $data['recentActivities'] = ActivityLog::query()->latest()->where(function($query) use ($companyId, $employeeIds) {
            $query->where('model_type', 'Company')
                ->where('model_id', $companyId)
                ->orWhere(function($query) use ($employeeIds) {
                    $query->where('model_type', 'Employee')
                        ->whereIn('model_id', $employeeIds);
                });
        })->take(10)->get();

        $data['userEmployeeCount'] = Employee::query()->where('company_id', $companyId)
            ->count();

        return $data;
    }
}
