<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Mail\CompanyAdded;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CompanyApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); // API istekleri için auth middleware
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = Company::query();

        if ($request->input('include_trashed') === 'only_trashed') {
            $query->onlyTrashed();
        } elseif ($request->input('include_trashed') === 'on') {
            $query->withTrashed();
        }

        if ($request->has('query')) {
            $searchTerm = '%' . $request->input('query') . '%';
            $query->where(function ($subQuery) use ($searchTerm) {
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

        $companies = $query->paginate($perPage)->appends([
            'query' => $request->input('query'),
            'include_trashed' => $request->input('include_trashed'),
            'sort' => $sort,
            'direction' => $direction,
            'per_page' => $perPage,
        ]);

        return response()->json($companies);
    }

    public function store(CompanyRequest $request)
    {
        $validatedData = $request->validated();
        
        // Check if user already has a company
        $user = User::find($validatedData['user_id']);
        if ($user->company_id) {
            return response()->json(['error' => 'The selected user is already assigned to a company.'], 400);
        }

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
        
        if ($request->has('user_id') && $request->user_id) {
            $company->user_id = $request->user_id;
        }
        
        $company->save();
        
        // Send email notification
        Mail::to('20comp1013@isik.edu.tr')->send(new CompanyAdded($company));
        
        return response()->json(['message' => 'Company created successfully.'], 201);
    }

    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

        return response()->json($company);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

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

        $company->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'website' => $request->input('website'),
            'logo' => $logoPath,
        ]);

        // assign user to company
        if ($request->user_id) {
            $user = User::find($request->user_id);
            if ($user) {
                $user->company_id = $company->id;
                $user->save();
            }
        }

        return response()->json(['message' => 'Company updated successfully!']);
    }

    public function destroy($id)
    {
        $company = Company::withTrashed()->find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found.'], 404);
        }

        if ($company->trashed()) {
            $company->forceDelete();
            return response()->json(['message' => 'Company permanently deleted!']);
        } else {
            $company->delete();
            return response()->json(['message' => 'Company deleted!']);
        }
    }

    public function restore($id)
    {
        $company = Company::withTrashed()->find($id);

        if (!$company || !$company->trashed()) {
            return response()->json(['error' => 'Company not found or not deleted.'], 404);
        }

        $company->restore();
        return response()->json(['message' => 'Company restored successfully.']);
    }
}
