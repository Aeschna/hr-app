<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyStoreFormRequest;
use App\Http\Resources\CompanyResource;
use App\Mail\CompanyAdded;
use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function __construct(public readonly CompanyRepository $repository)
    {

    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return CompanyResource::collection($this->repository->getPaginatedData($request));
    }

    public function store(CompanyStoreFormRequest $request)
    {
        $validatedData = $request->validated();

        // Check if user already has a company
        $user = User::find($validatedData['user_id']);
        if ($user->company_id) {
            return response()->json(['error' => 'The selected user is already assigned to a company.'], 400);
        }

        $company          = new Company();
        $company->name    = $validatedData['name'];
        $company->address = $validatedData['address'];
        $company->phone   = $validatedData['phone'];
        $company->email   = $validatedData['email'];

        if ($request->hasFile('logo')) {
            $logo     = $request->file('logo');
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

    /**
     * @param Company $company
     *
     * @return CompanyResource
     */
    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company);
    }

    public function update(Request $request, Company $company)
    {
        // move to form request
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email',
            'website' => 'nullable|url',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete('public/' . $company->logo);
            }

            $logo     = $request->file('logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/logos', $logoName);
            $logoPath = 'logos/' . $logoName;
        } else {
            $logoPath = $company->logo;
        }

        $company->update([
            'name'    => $request->input('name'),
            'address' => $request->input('address'),
            'phone'   => $request->input('phone'),
            'email'   => $request->input('email'),
            'website' => $request->input('website'),
            'logo'    => $logoPath,
        ]);

        // assign user to company
        $user = User::query()->find($request->input('user_id'));
        if ($user) {
            $user->company_id = $company->id;
            $user->save();
        }

        return response()->json(['message' => 'Company updated successfully!']);
    }

    /**
     * @param Company $company
     *
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        if ($company->trashed()) {
            $company->forceDelete();

            return response()->json(['message' => 'Company permanently deleted!']);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted!']);
    }

    /**
     * @param Company $company
     *
     * @return JsonResponse
     */
    public function restore(Company $company): JsonResponse
    {
        $company->restore();

        return response()->json(['message' => 'Company restored successfully.']);
    }
}
