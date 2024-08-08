<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeAdded;
use App\Mail\EmployeeUpdated;
use App\Mail\EmployeeSoftDeleted;
use App\Mail\EmployeeRestored;
use App\Mail\EmployeePermanentlyDeleted;


class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
{
    
    $breadcrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Employees', 'url' => route('employees.index')],
    ];

    $perPageOptions = [10, 50, 100];
    $perPage = $request->input('per_page', 10);
    $query = Employee::query();

    

    // get user's linked company
    $user = Auth::user();
    $companyId = $user->company_id;

    

    // show only employees which belongs to company
    $query->where('company_id', $companyId);

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

    $sort = $request->input('sort', 'first_name');
    $direction = $request->input('direction', 'asc');

    if ($sort === 'company') {
        $query->join('companies', 'employees.company_id', '=', 'companies.id')
              ->orderBy('companies.name', $direction)
              ->select('employees.*');
    } else {
        $query->orderBy($sort, $direction);
    }

    $employees = $query->paginate($perPage);

    return view('employees.index', compact('employees', 'perPageOptions', 'perPage', 'sort', 'direction', 'breadcrumbs'));
}


    public function create()
    {

        $user = auth()->user();
    $company = $user->company; 

        
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Employees', 'url' => route('employees.index')],
            ['name' => 'Create', 'url' => '#'],
        ];

        $companies = Company::all();
        return view('employees.create', compact('companies','company', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        // Validate the form data
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
    
        // Create a new employee
        $employee = Employee::create($request->all());
    
        // Send email notification
        Mail::to('example@example.com')->send(new EmployeeAdded($employee));
    
        // Redirect and show success message
        return redirect()->route('employees.index')->with('status', 'Employee created successfully!');
    }
    


    public function show(Employee $employee)
    {
       
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Employees', 'url' => route('employees.index')],
            ['name' => 'Show', 'url' => '#'],
        ];

        return view('employees.show', compact('employee', 'breadcrumbs'));
    }

    public function edit(Employee $employee)
    {
        
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Employees', 'url' => route('employees.index')],
            ['name' => 'Edit', 'url' => '#'],
        ];

        $companies = Company::all();
        return view('employees.edit', compact('employee', 'companies', 'breadcrumbs'));
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

   
    $employee->update([
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
        'phone' => $request->input('phone'),
        'company_id' => $request->input('company_id'),
    ]);
    Mail::to('20comp1013@isik.edu.tr')->send(new EmployeeUpdated($employee));
    return redirect()->route('employees.index')->with('status', 'Employee updated successfully!');
}


    public function destroy($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        if ($employee->trashed()) {
            $employee->forceDelete();
            session()->flash('status', 'Employee permanently deleted!');
            // Send email notification for permanent delete
        Mail::to('20comp1013@isik.edu.tr')->send(new EmployeePermanentlyDeleted($employee));
        } else {
            $employee->delete();
            session()->flash('status', 'Employee deleted!');
            Mail::to('20comp1013@isik.edu.tr')->send(new EmployeeSoftDeleted($employee));
        }

        return redirect()->route('employees.index');
    }

    public function forceDelete($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        if ($employee->trashed()) {
            $employee->forceDelete();
            session()->flash('status', 'Employee permanently deleted!');
            Mail::to('20comp1013@isik.edu.tr')->send(new EmployeePermanentlyDeleted($employee));
        } else {
            session()->flash('status', 'Employee is not deleted yet, soft delete the employee first!');
        }

        return redirect()->route('employees.index');
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        Mail::to('20comp1013@isik.edu.tr')->send(new EmployeeRestored($employee));
        return redirect()->route('employees.index')->with('status', 'Employee restored successfully.');
    }
}
