<?php

namespace App\Http\Controllers;

// İlgili Request sınıfı oluşturulmalı
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Users', 'url' => route('users.index')],
        ];

        $perPageOptions = [10, 50, 100];
        $perPage        = $request->input('per_page', 10);
        $query          = User::query();

        // Get current user
        $user      = Auth::user();
        $isAdmin   = $user->is_admin; // Check if the user is an admin
        $companyId = $user->company_id;

        // Show all users if the user is an admin, otherwise filter by the user's company
        if (!$isAdmin) {
            $query->where('company_id', $companyId);
        }

        if ($request->input('include_trashed') == 'on') {
            $query->withTrashed();
        }

        if ($request->has('query')) {
            $searchTerm = '%' . $request->input('query') . '%';
            $query->where(function($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
            });
        }

        $sort      = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        $query->orderBy($sort, $direction);

        $users = $query->paginate($perPage)->appends([
            'query'           => $request->input('query'),
            'include_trashed' => $request->input('include_trashed'),
            'sort'            => $sort,
            'direction'       => $direction,
            'per_page'        => $perPage,
        ]);

        return view('users.index', compact('users', 'perPageOptions', 'perPage', 'sort', 'direction', 'breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Users', 'url' => route('users.index')],
            ['name' => 'Create', 'url' => '#'],
        ];

        $companies = Company::all();

        return view('users.create', compact('companies', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[\w\.-]+@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.com|aol\.com|example\.org|example\.net)$/i',
            ],
            'company_id' => 'required|exists:companies,id',
        ]);

        // Create a new user
        $user = User::create($request->all());

       
        

        // Redirect and show success message
        return redirect()->route('users.index')->with('status', 'User created successfully!');
    }

    public function show(User $user)
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Users', 'url' => route('users.index')],
            ['name' => 'Show', 'url' => '#'],
        ];

        return view('users.show', compact('user', 'breadcrumbs'));
    }

    public function edit(User $user)
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Users', 'url' => route('users.index')],
            ['name' => 'Edit', 'url' => '#'],
        ];

        $companies = Company::all();

        return view('users.edit', compact('user', 'companies', 'breadcrumbs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'regex:/^[\w\.-]+@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.com|aol\.com|example\.org|example\.net)$/i',
            ],
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name'       => $request->input('name'),
            'email'      => $request->input('email'),
            'company_id' => $request->input('company_id'),
        ]);

       

        return redirect()->route('users.index')->with('status', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->forceDelete();
            session()->flash('status', 'User permanently deleted!');
            // Send email notification for permanent delete
           
        } else {
            $user->delete();
            session()->flash('status', 'User deleted!');
           
        }

        return redirect()->route('users.index');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->forceDelete();
            session()->flash('status', 'User permanently deleted!');
           
        } else {
            session()->flash('status', 'User is not deleted yet, soft delete the user first!');
        }

        return redirect()->route('users.index');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
       
        return redirect()->route('users.index')->with('status', 'User restored successfully.');
    }

    public function getAuthenticatedUser()
    {
        return response()->json([
            'company' => auth()->user()->company,
            'isAdmin' => auth()->user()->isAdmin(),
        ]);
    }
}
