@extends('layouts.app')

@section('content')

<div class="container">
    

    
        <h2>Users</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
   

    <!-- Search Form -->
    <form id="searchForm" method="GET" class="mt-3">
        <div class="input-group mb-3">
            <input type="text" name="query" class="form-control" placeholder="Search users..." value="{{ request('query') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <!-- Dropdown Button for Results Per Page and Include Deleted Button -->
    <form id="resultsPerPageForm" action="{{ route('users.index') }}" method="GET" class="form-inline mb-3">
        <div class="form-group mr-3">
            <label for="per_page" class="mr-2">Results per page:</label>
            <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <!-- Hidden Checkbox for Including Trashed Records -->
        <input type="hidden" name="include_trashed" id="include_trashed" value="{{ request('include_trashed', 'off') }}">

        <!-- Button to Apply Include Deleted -->
        <button type="button" id="includeDeletedButton" class="btn {{ request('include_trashed') == 'on' ? 'btn-dark' : 'btn-secondary' }} ml-3" onclick="toggleIncludeTrashed()">
            Include Deleted
        </button>
    </form>

    @if (request()->has('query'))
        <a href="{{ route('users.index') }}" class="btn btn-secondary mb-3">Back to Users</a>
    @endif
    
    <table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th style="width: 30%;"> <!-- Belirli bir genişlik -->
                <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                    Name
                </a>
            </th>
            <th style="width: 30%;">
                <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'email', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                    Email
                </a>
            </th>
            <th style="width: 30%;">
                <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'company', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                    Company
                </a>
            </th>
            <th style="width: 10%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr class="{{ $user->trashed() ? 'table-danger' : '' }}">
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->company->name ?? 'N/A' }}</td>
            <td>
                <div class="d-flex flex-column align-items-start">
                    @if($user->trashed())
                        <div class="mb-2">
                            <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                        </div>
                        <div>
                            <form action="{{ route('users.forceDelete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Force Delete</button>
                            </form>
                        </div>
                    @else
                        <div class="mb-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm">Edit User</a>
                        </div>
                        <div>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $users->appends(request()->except('page'))->previousPageUrl() }}" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>

            @for ($i = 1; $i <= $users->lastPage(); $i++)
                <li class="page-item {{ $i == $users->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $users->appends(request()->except('page'))->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $users->appends(request()->except('page'))->nextPageUrl() }}" aria-label="Next">
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
        var searchUrl = `{{ route('users.index') }}?per_page=${perPage}&query=${encodeURIComponent(query)}`;

        // Preserve the include_trashed state in the URL
        if (includeTrashed === 'on') {
            searchUrl += `&include_trashed=on`;
        }

        window.location.href = searchUrl;
    };

    function confirmDelete() {
        return confirm('Are you sure you want to delete this user?');
    }
</script>

@endsection
