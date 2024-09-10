<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeAdded;
use App\Mail\EmployeeUpdated;
use App\Mail\EmployeeSoftDeleted;
use App\Mail\EmployeeRestored;
use App\Mail\EmployeePermanentlyDeleted;

class EmployeeApiController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::paginate($request->per_page); // Retrieve all employees

        return response()->json($employees); // Return employees as JSON
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'regex:/^[\w\.-]+@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.com|aol\.com|example\.org|example\.net)$/i'
            ],
            'phone' => 'required|string|max:20|regex:/^\+?[0-9\s\-\(\)]+$/',
            'company_id' => 'required|exists:companies,id',
        ]);

        $employee = Employee::create($request->all());

        Mail::to('example@example.com')->send(new EmployeeAdded($employee));

        return response()->json(['status' => 'Employee created successfully!', 'employee' => $employee], 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'regex:/^[\w\.-]+@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.com|aol\.com|example\.org|example\.net)$/i'
            ],
            'phone' => 'required|string|max:20|regex:/^\+?[0-9\s\-\(\)]+$/',
            'company_id' => 'required|exists:companies,id',
        ]);

        $employee = Employee::findOrFail($id);

        $employee->update($request->all());

        Mail::to('20comp1013@isik.edu.tr')->send(new EmployeeUpdated($employee));

        return response()->json(['status' => 'Employee updated successfully!', 'employee' => $employee]);
    }

    public function destroy($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        if ($employee->trashed()) {
            $employee->forceDelete();
            Mail::to('20comp1013@isik.edu.tr')->send(new EmployeePermanentlyDeleted($employee));
            return response()->json(['status' => 'Employee permanently deleted!']);
        } else {
            $employee->delete();
            Mail::to('20comp1013@isik.edu.tr')->send(new EmployeeSoftDeleted($employee));
            return response()->json(['status' => 'Employee deleted!']);
        }
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        Mail::to('20comp1013@isik.edu.tr')->send(new EmployeeRestored($employee));

        return response()->json(['status' => 'Employee restored successfully!']);
    }

    public function forceDelete($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        if ($employee->trashed()) {
            $employee->forceDelete();
            Mail::to('20comp1013@isik.edu.tr')->send(new EmployeePermanentlyDeleted($employee));
            return response()->json(['status' => 'Employee permanently deleted!']);
        }

        return response()->json(['status' => 'Employee is not deleted yet, soft delete the employee first!']);
    }
}
