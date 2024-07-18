@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Company Details</h2>
    <div class="card mt-4">
        <div class="card-header">
            {{ $company->name }}
        </div>
        <div class="card-body">
            <p><strong>Address:</strong> {{ $company->address }}</p>
            <p><strong>Phone:</strong> {{ $company->phone }}</p>
            <p><strong>Email:</strong> {{ $company->email }}</p>
            <p><strong>Website:</strong> <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></p>
            @if($company->logo)
                <p><strong>Logo:</strong></p>
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" style="width: 100px; height: 100px;">
            @endif
        </div>
    </div>
    <a href="{{ route('companies.index') }}" class="btn btn-primary mt-3">Back to Companies</a>
</div>
@endsection
