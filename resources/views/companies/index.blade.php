@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Companies</h2>
    <a href="{{ route('companies.create') }}" class="btn btn-primary">Add Company</a>


    <!-- Search Form -->
    <form action="{{ route('companies.search') }}" method="GET" class="mt-3">
        <div class="input-group mb-3">
            <input type="text" name="query" class="form-control" placeholder="Search companies...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <!-- Dropdown Button for Results Per Page -->
    <form action="{{ route('companies.index') }}" method="GET" class="form-inline mb-3">
        <label for="per_page" class="mr-2">Results per page: </label>
        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    </form>

    @if (request()->has('query'))
        <a href="{{ route('companies.index') }}" class="btn btn-secondary mb-3">Back to Companies</a>
    @endif
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Logo</th>
                <th>Website</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
            <tr>
                <td>{{ $company->name }}</td>
                <td>{{ $company->address }}</td>
                <td>{{ $company->phone }}</td>
                <td>{{ $company->email }}</td>
                <td><img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" width="100" height="100"></td>
                <td><a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></td>
                <td>
                    <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No companies found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

<!-- pagination -->

    <nav aria-label="Page navigation">
    <ul class="pagination pagination-sm">
        <li class="page-item {{ $companies->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $companies->previousPageUrl() }}" aria-label="Previous">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>

        @for ($i = 1; $i <= $companies->lastPage(); $i++)
            <li class="page-item {{ $i == $companies->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $companies->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        <li class="page-item {{ $companies->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $companies->nextPageUrl() }}" aria-label="Next">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>


</div>
@endsection
