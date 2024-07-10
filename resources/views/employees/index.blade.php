@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Employees</h2>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>
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
                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info">View</a>
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
    {{ $employees->links() }}
</div>
@endsection
