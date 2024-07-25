@extends('layouts.app')

@section('content')
<div class="container">
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
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="search_in_all" name="search_in_all" {{ request('search_in_all') == 'on' ? 'checked' : '' }}>
            <label class="form-check-label" for="search_in_all">Search in all records (including deleted)</label>
        </div>
    </form>

    <!-- Dropdown Button for Results Per Page -->
    <form id="resultsPerPageForm" action="{{ route('employees.index') }}" method="GET" class="form-inline mb-3">
        <label for="per_page" class="mr-2">Results per page: </label>
        <select name="per_page" id="per_page" class="form-control" onchange="updateResultsPerPage()">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>

        <!-- Hidden Checkbox for Including Trashed Records -->
        <input type="checkbox" id="include_trashed" name="include_trashed" style="display: none;" {{ request('include_trashed') == 'on' ? 'checked' : '' }}>

        <!-- Button to Apply Include Deleted -->
        <button type="button" id="includeDeletedButton" class="btn ml-3" onclick="toggleIncludeTrashed()">Include Deleted</button>
    </form>

    @if (request()->has('query'))
        <a href="{{ route('employees.index') }}" class="btn btn-secondary mb-3">Back to Employees</a>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
            <tr>
                <td>{{ $employee->first_name }}</td>
                <td>{{ $employee->last_name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->company->name }}</td>
                <td>
                    @if($employee->trashed())
                        <form action="{{ route('employees.restore', $employee->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                    @else
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline;">
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
                <a class="page-link" href="{{ $employees->previousPageUrl() }}" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>

            @for ($i = 1; $i <= $employees->lastPage(); $i++)
                <li class="page-item {{ $i == $employees->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $employees->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ $employees->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $employees->nextPageUrl() }}" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>

<script>
    var isIncludeTrashedActive = {{ request('include_trashed') == 'on' ? 'true' : 'false' }};

    function updateResultsPerPage() {
        var form = document.getElementById('resultsPerPageForm');
        form.submit();
    }

    function toggleIncludeTrashed() {
        var form = document.getElementById('resultsPerPageForm');
        var includeTrashedCheckbox = document.getElementById('include_trashed');
        var button = document.getElementById('includeDeletedButton');

        // Toggle checkbox state
        includeTrashedCheckbox.checked = !includeTrashedCheckbox.checked;

        // Change button color based on checkbox state
        if (includeTrashedCheckbox.checked) {
            button.classList.remove('btn-secondary');
            button.classList.add('btn-dark');
        } else {
            button.classList.remove('btn-dark');
            button.classList.add('btn-secondary');
        }

        // Submit the form
        form.submit();
    }

    // Initialize button color based on current checkbox state
    document.addEventListener('DOMContentLoaded', function() {
        var button = document.getElementById('includeDeletedButton');
        if ({{ request('include_trashed') == 'on' ? 'true' : 'false' }}) {
            button.classList.add('btn-dark');
        } else {
            button.classList.add('btn-secondary');
        }
    });

    document.getElementById('searchForm').onsubmit = function(event) {
        event.preventDefault();
        var query = this.query.value;
        var perPage = document.getElementById('per_page').value;
        var searchInAll = document.getElementById('search_in_all').checked ? 'on' : 'off';
        var includeTrashed = document.getElementById('include_trashed').checked ? 'on' : 'off';
        var searchUrl = `http://127.0.0.1:8000/employees?per_page=${perPage}&query=${encodeURIComponent(query)}`;

        // Preserve the include_trashed state in the URL
        if (searchInAll === 'on') {
            searchUrl += `&include_trashed=on`;
        }

        window.location.href = searchUrl;
    };
</script>
@endsection
