<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $perPageOptions = [10, 50, 100];
    $perPage = $request->input('per_page', 10);

    $query = Company::query();

    if ($request->input('include_trashed') == 'on') {
        $query->withTrashed();
    }

    if ($request->has('query')) {
        $query->where(function ($subQuery) use ($request) {
            $searchTerm = '%' . $request->input('query') . '%';
            $subQuery->where('name', 'like', $searchTerm)
                     ->orWhere('address', 'like', $searchTerm)
                     ->orWhere('phone', 'like', $searchTerm)
                     ->orWhere('email', 'like', $searchTerm)
                     ->orWhere('website', 'like', $searchTerm);
        });
    }

    // Sıralama
    $sort = $request->input('sort', 'name');
    $direction = $request->input('direction', 'asc');
    $query->orderBy($sort, $direction);

    $companies = $query->paginate($perPage);

    return view('companies.index', compact('companies', 'perPageOptions', 'perPage', 'sort', 'direction'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $validatedData = $request->validated();

        $company = new Company();
        $company->name = $validatedData['name'];
        $company->address = $validatedData['address'];
        $company->phone = $validatedData['phone'];
        $company->email = $validatedData['email'];

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/logos', $logoName);
            $company->logo = 'logos/' . $logoName;
        }

        $company->website = $validatedData['website'];
        $company->save();

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete('public/' . $company->logo);
            }

            $logo = $request->file('logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/logos', $logoName);
            $validatedData['logo'] = 'logos/' . $logoName;
        }

        $company->update($validatedData);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $company = Company::withTrashed()->findOrFail($id);

    if ($company->trashed()) {
        $company->forceDelete();
        session()->flash('status', 'Company permanently deleted!');
    } else {
        $company->delete();
        session()->flash('status', 'Company deleted!');
    }

    return redirect()->route('companies.index');
}

    public function forceDelete($id)
{
    $company = Company::withTrashed()->findOrFail($id);

    if ($company->trashed()) {
        $company->forceDelete();
        session()->flash('status', 'Company permanently deleted!');
    } else {
        session()->flash('status', 'Company is not deleted yet, soft delete the employee first!');
    }

    return redirect()->route('companies.index');
}


    public function restore($id)
    {
        $company = Company::withTrashed()->find($id);

        if ($company && $company->trashed()) {
            $company->restore();
            return redirect()->route('companies.index')->with('success', 'Company restored successfully.');
        }

        return redirect()->route('companies.index')->with('error', 'Company not found or not deleted.');
    }

    public function search(Request $request)
{
    $query = $request->input('query');
    $perPageOptions = [10, 50, 100];
    $perPage = $request->input('per_page', 10);

    $companies = Company::where('name', 'like', "%$query%")
        ->orWhere('address', 'like', "%$query%")
        ->orWhere('phone', 'like', "%$query%")
        ->orWhere('email', 'like', "%$query%")
        ->orWhere('website', 'like', "%$query%" )->paginate($perPage);
         
         return view('companies.index', compact('companies', 'perPageOptions', 'perPage'));
    
}
    

    
}
