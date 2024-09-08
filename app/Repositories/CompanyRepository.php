<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CompanyRepository
{
    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginatedData(Request $request): LengthAwarePaginator
    {
        return Company::query()
            ->when($request->has('include_trashed'), function ($q) use($request) {
                if($request->input('include_trashed')  === 'only_trashed') {
                    return $q->onlyTrashed();
                }

                if($request->input('include_trashed')  === 'on') {
                    return $q->withTrashed();
                }
            })
            ->when($request->has('query'), function ($q) use($request) {
                $searchTerm = '%' . $request->input('query') . '%';

                return $q->where(function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', $searchTerm)
                        ->orWhere('address', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('website', 'like', $searchTerm);
                });
            })
            ->orderBy(
                column: $request->input('sort', 'name'),
                direction: $request->input('direction', 'asc')
            )
            ->paginate($request->input('per_page', 10));
    }
}
