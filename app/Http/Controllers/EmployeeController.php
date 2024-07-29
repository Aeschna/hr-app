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
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Breadcrumb verileri
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Employees', 'url' => route('employees.index')],
        ];

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
        // Breadcrumb verileri
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Employees', 'url' => route('employees.index')],
            ['name' => 'Create', 'url' => '#'],
        ];

        $companies = Company::all();
        return view('employees.create', compact('companies', 'breadcrumbs'));
    }

    public function store(Request $request)
{
    // Form verilerini doğrulayın
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'required|string|max:20',
        'company_id' => 'required|exists:companies,id',
    ]);

    // Yeni kullanıcıyı oluşturun
    Employee::create($request->all());

    // Kullanıcıyı yönlendirin ve başarılı bildirim mesajı gösterin
    return redirect()->route('employees.index')->with('status', 'Employee created successfully!');
}


    public function show(Employee $employee)
    {
        // Breadcrumb verileri
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Employees', 'url' => route('employees.index')],
            ['name' => 'Show', 'url' => '#'],
        ];

        return view('employees.show', compact('employee', 'breadcrumbs'));
    }

    public function edit(Employee $employee)
    {
        // Breadcrumb verileri
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
        'email' => 'nullable|email',
        'phone' => 'required|string|max:20',
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

    return redirect()->route('employees.index')->with('status', 'Employee updated successfully!');
}


    public function destroy($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        if ($employee->trashed()) {
            $employee->forceDelete();
            session()->flash('status', 'Employee permanently deleted!');
        } else {
            $employee->delete();
            session()->flash('status', 'Employee deleted!');
        }

        return redirect()->route('employees.index');
    }

    public function forceDelete($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        if ($employee->trashed()) {
            $employee->forceDelete();
            session()->flash('status', 'Employee permanently deleted!');
        } else {
            session()->flash('status', 'Employee is not deleted yet, soft delete the employee first!');
        }

        return redirect()->route('employees.index');
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('employees.index')->with('status', 'Employee restored successfully.');
    }
}
