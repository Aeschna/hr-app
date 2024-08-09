@extends('layouts.app')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<div class="container">
    <h2>Companies</h2>
    <a href="{{ route('companies.create') }}" class="btn btn-primary">Add Company</a>

    <!-- Search Form -->
    <form id="searchForm" method="GET" class="mt-3">
        <div class="input-group mb-3">
            <input type="text" name="query" class="form-control" placeholder="Search companies..." value="{{ request('query') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <!-- Dropdown Button for Results Per Page and Include Deleted Button -->
    <form id="resultsPerPageForm" action="{{ route('companies.index') }}" method="GET" class="form-inline mb-3">
        <label for="per_page" class="mr-2">Results per page: </label>
        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>

        <!-- Hidden Checkbox for Including Trashed Records -->
        <div class="form-check ml-3" style="display: none;">
            <input type="checkbox" class="form-check-input" id="include_trashed" name="include_trashed" {{ request('include_trashed') == 'on' ? 'checked' : '' }}>
        </div>

        <!-- Button to Apply Include Deleted -->
        <button type="button" id="includeDeletedButton"
            class="btn {{ request('include_trashed') == 'on' ? 'btn-dark' : 'btn-secondary' }} ml-3"
            onclick="toggleIncludeTrashed()">Include Deleted</button>
    </form>

    @if (request()->has('query'))
        <a href="{{ route('companies.index') }}" class="btn btn-secondary mb-3">Back to Companies</a>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('companies.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                </th>
                <th>
                    <a href="{{ route('companies.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'email', 'direction' => $sort === 'email' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                </th>
                <th>
                    <a href="{{ route('companies.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'logo', 'direction' => $sort === 'logo' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Logo</a>
                </th>
                <th>
                    <a href="{{ route('companies.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'website', 'direction' => $sort === 'website' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Website</a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
            <tr class="{{ $company->trashed() ? 'trashed' : '' }}">
                <td>{{ $company->name }}</td>
                <td>{{ $company->email }}</td>
                <td>
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" class="img-fluid rounded mx-auto d-block" style="max-width: 100px; max-height: 100px;">
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $company->website }}</td>
                <td>
                    @if($company->trashed())
                        <form action="{{ route('companies.restore', $company->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                        <form action="{{ route('companies.forceDelete', $company->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this company?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Force Delete</button>
                        </form>
                    @else
                        <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-info">Edit</a>
                        <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm justify-content-center">
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

<script>
    function toggleIncludeTrashed() {
        var form = document.getElementById('resultsPerPageForm');
        var includeTrashedCheckbox = document.getElementById('include_trashed');
        var includeDeletedButton = document.getElementById('includeDeletedButton');
        includeTrashedCheckbox.checked = !includeTrashedCheckbox.checked;
        includeDeletedButton.classList.toggle('btn-dark');
        includeDeletedButton.classList.toggle('btn-secondary');
        form.submit();
    }

    document.getElementById('searchForm').onsubmit = function (event) {
        event.preventDefault();
        var query = this.query.value;
        var perPage = document.getElementById('per_page').value;
        var includeTrashed = document.getElementById('include_trashed').checked ? 'on' : 'off';
        var searchUrl = `{{ route('companies.index') }}?per_page=${perPage}&query=${encodeURIComponent(query)}`;

        // Preserve the include_trashed state in the URL
        if (includeTrashed === 'on') {
            searchUrl += `&include_trashed=on`;
        }

        window.location.href = searchUrl;
    };

    function confirmDelete() {
        return confirm('Are you sure you want to delete this company?');
    }
</script>

@endsection
