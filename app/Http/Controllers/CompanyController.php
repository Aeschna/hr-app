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

    public function index(Request $request)
    {
        // Breadcrumb verileri
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Companies', 'url' => route('companies.index')],
        ];

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

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $companies = $query->paginate($perPage);

        return view('companies.index', compact('companies', 'perPageOptions', 'perPage', 'sort', 'direction', 'breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home'), 'active' => false],
            ['name' => 'Companies', 'url' => route('companies.index'), 'active' => false],
            ['name' => 'Create', 'url' => '', 'active' => true],
        ];

        return view('companies.create', compact('breadcrumbs'));
    }

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

        return redirect()->route('companies.index')->with('status', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Companies', 'url' => route('companies.index')],
            ['name' => 'Show', 'url' => '#'],
        ];

        return view('companies.show', compact('company', 'breadcrumbs'));
    }

    public function edit(Company $company)
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home'), 'active' => false],
            ['name' => 'Companies', 'url' => route('companies.index'), 'active' => false],
            ['name' => 'Edit', 'url' => '', 'active' => true],
        ];

        return view('companies.edit', compact('breadcrumbs', 'company'));
    }

    public function update(Request $request, $id)
    {
        // Veritabanı doğrulama kuralları
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        // Şirketi bul
        $company = Company::findOrFail($id);
    
        // Eğer logo dosyası varsa, eski logoyu sil ve yeni logoyu yükle
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete('public/' . $company->logo);
            }
    
            $logo = $request->file('logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/logos', $logoName);
            $logoPath = 'logos/' . $logoName;
        } else {
            $logoPath = $company->logo;
        }
    
        // Şirketi güncelle
        $company->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'website' => $request->input('website'),
            'logo' => $logoPath,
        ]);
    
        return redirect()->route('companies.index')->with('status', 'Company updated successfully!');
    }
    

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
            session()->flash('status', 'Company is not deleted yet, soft delete the company first!');
        }

        return redirect()->route('companies.index');
    }

    public function restore($id)
    {
        $company = Company::withTrashed()->find($id);

        if ($company && $company->trashed()) {
            $company->restore();
            return redirect()->route('companies.index')->with('status', 'Company restored successfully.');
        }

        return redirect()->route('companies.index')->with('status', 'Company not found or not deleted.');
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
            ->orWhere('website', 'like', "%$query%")
            ->paginate($perPage);

        return view('companies.index', compact('companies', 'perPageOptions', 'perPage'));
    }
}
