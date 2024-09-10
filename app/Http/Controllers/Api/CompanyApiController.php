<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyApiController extends Controller
{
    public function index(Request $request)
    {
        // API'ye uygun şekilde veri döndürün
        $companies = Company::paginate($request->per_page);
        return response()->json($companies);
    }

    public function store(Request $request)
    {
        // Yeni şirket oluşturma işlemi
        $company = Company::create($request->all());
        return response()->json($company, 201);
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->all());
        return response()->json($company);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return response()->json(null, 204);
    }
    public function edit($id)
{
    $company = Company::withTrashed()->find($id);

    if (!$company) {
        return response()->json(['error' => 'Company not found'], 404);
    }

    return response()->json($company);
}

}
