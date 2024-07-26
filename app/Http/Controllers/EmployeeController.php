<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods
    }

    public function index(Request $request)
{
    $perPageOptions = [10, 50, 100];
    $perPage = $request->input('per_page', 10);

    $query = Employee::query();

    if ($request->input('include_trashed') == 'on') {
        $query->withTrashed();
    }

    if ($request->has('query')) {
        $query->where(function ($subQuery) use ($request) {
            $searchTerm = '%' . $request->input('query') . '%';
            $subQuery->where('first_name', 'like', $searchTerm)
                     ->orWhere('last_name', 'like', $searchTerm)
                     ->orWhere('email', 'like', $searchTerm)
                     ->orWhere('phone', 'like', $searchTerm)
                     ->orWhereHas('company', function ($q) use ($searchTerm) {
                         $q->where('name', 'like', $searchTerm);
                     });
        });
    }

    // Sıralama
    $sort = $request->input('sort', 'first_name'); // Varsayılan sıralama sütunu
    $direction = $request->input('direction', 'asc'); // Varsayılan sıralama yönü

    if ($sort === 'company') {
        $query->join('companies', 'employees.company_id', '=', 'companies.id')
              ->orderBy('companies.name', $direction)
              ->select('employees.*');
    } else {
        $query->orderBy($sort, $direction);
    }

    $employees = $query->paginate($perPage);

    return view('employees.index', compact('employees', 'perPageOptions', 'perPage', 'sort', 'direction'));
}


    

    public function create()
    {
        $companies = Company::all();
        return view('employees.create', compact('companies'));
    }

    public function store(EmployeeRequest $request)
    {
        $validatedData = $request->validated();
        Employee::create($validatedData);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $companies = Company::all();
        return view('employees.edit', compact('employee', 'companies'));
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        $validatedData = $request->validated();
        $employee->update($validatedData);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('employees.index')->with('success', 'Employee restored successfully.');
    }
}
