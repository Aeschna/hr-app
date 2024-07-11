@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Companies</h2>
    <a href="{{ route('companies.create') }}" class="btn btn-primary">Add Company</a>
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
                    <a href="{{ route('companies.show', $company->id) }}" class="btn btn-info">View</a>
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
    {{ $companies->links() }}
</div>
@endsection
