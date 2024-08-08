@extends('layouts.app')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<div class="container">
    <div class="my-4 p-3 bg-light border rounded">
        <h2 class="text-center mb-0">
            {{ auth()->user()->company->name ?? 'No Company Assigned' }}
        </h2>
    </div>
</div>



    <h2>Employees</h2>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>

    <!-- Search Form -->
    <form id="searchForm" method="GET" class="mt-3">
        <div class="input-group mb-3">
            <input type="text" name="query" class="form-control" placeholder="Search employees..." value="{{ request('query') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <!-- Dropdown Button for Results Per Page and Include Deleted Button -->
    <form id="resultsPerPageForm" action="{{ route('employees.index') }}" method="GET" class="form-inline mb-3">
        <label for="per_page" class="mr-2">Results per page: </label>
        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>

        <!-- Hidden Checkbox for Including Trashed Records -->
        <input type="hidden" name="include_trashed" id="include_trashed" value="{{ request('include_trashed', 'off') }}">

        <!-- Button to Apply Include Deleted -->
        <button type="button" id="includeDeletedButton" class="btn {{ request('include_trashed') == 'on' ? 'btn-dark' : 'btn-secondary' }} ml-3" onclick="toggleIncludeTrashed()">Include Deleted</button>
    </form>

    @if (request()->has('query'))
        <a href="{{ route('employees.index') }}" class="btn btn-secondary mb-3">Back to Employees</a>
    @endif
    
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('employees.index', array_merge(request()->all(), ['sort' => 'first_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        First Name
                    </a>
                </th>
                <th>
                    <a href="{{ route('employees.index', array_merge(request()->all(), ['sort' => 'last_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Last Name
                    </a>
                </th>
                <th>
                    <a href="{{ route('employees.index', array_merge(request()->all(), ['sort' => 'email', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Email
                    </a>
                </th>
                <th>
                    <a href="{{ route('employees.index', array_merge(request()->all(), ['sort' => 'phone', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Phone
                    </a>
                </th>
                <th>
                    <a href="{{ route('employees.index', array_merge(request()->all(), ['sort' => 'company', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Company
                    </a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
            <tr class="{{ $employee->trashed() ? 'trashed' : '' }}">
                <td>{{ $employee->first_name }}</td>
                <td>{{ $employee->last_name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->company->name ?? 'N/A' }}</td>
                <td>
                    @if($employee->trashed())
                        <form action="{{ route('employees.restore', $employee->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                        <form action="{{ route('employees.forceDelete', $employee->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this employee?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Force Delete</button>
                        </form>
                    @else
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-info">Edit</a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
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
            <li class="page-item {{ $employees->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $employees->appends(request()->except('page'))->previousPageUrl() }}" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>

            @for ($i = 1; $i <= $employees->lastPage(); $i++)
                <li class="page-item {{ $i == $employees->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $employees->appends(request()->except('page'))->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ $employees->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $employees->appends(request()->except('page'))->nextPageUrl() }}" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>

<script>
    function toggleIncludeTrashed() {
        
        var includeTrashedCheckbox = document.getElementById('include_trashed');
        var includeDeletedButton = document.getElementById('includeDeletedButton');
        
        // Toggle the include_trashed value
        includeTrashedCheckbox.value = includeTrashedCheckbox.value === 'on' ? 'off' : 'on';
        
        // Toggle button classes
        includeDeletedButton.classList.toggle('btn-dark');
        includeDeletedButton.classList.toggle('btn-secondary');
        
        // Submit the form
        var form = document.getElementById('resultsPerPageForm');
        form.submit();
    }

    document.getElementById('searchForm').onsubmit = function(event) {
        event.preventDefault();
        var query = this.query.value;
        var perPage = document.getElementById('per_page').value;
        var includeTrashed = document.getElementById('include_trashed').value;
        var searchUrl = `{{ route('employees.index') }}?per_page=${perPage}&query=${encodeURIComponent(query)}`;

        // Preserve the include_trashed state in the URL
        if (includeTrashed === 'on') {
            searchUrl += `&include_trashed=on`;
        }

        window.location.href = searchUrl;
    };

    function confirmDelete() {
        return confirm('Are you sure you want to delete this employee?');
    }
</script>

@endsection
