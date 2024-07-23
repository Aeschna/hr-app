@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Employees</h2>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>
    <form action="{{ route('employees.search') }}" method="GET" class="mt-3">
        <div class="input-group mb-3">
            <input type="text" name="query" class="form-control" placeholder="Search employees...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

<!-- Dropdown Button for Results Per Page -->
<form action="{{ route('employees.index') }}" method="GET" class="form-inline mb-3">
        <label for="per_page" class="mr-2">Results per page: </label>
        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
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
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>

<!-- pagination -->

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
@endsection
