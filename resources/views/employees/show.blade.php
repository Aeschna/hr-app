@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Employee Details</h2>
    <div class="card">
        <div class="card-header">
            {{ $employee->first_name }} {{ $employee->last_name }}
        </div>
        <div class="card-body">
            <p>Email: {{ $employee->email }}</p>
            <p>Phone: {{ $employee->phone }}</p>
            <p>Company: {{ $employee->company->name }}</p>
        </div>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-primary mt-3">Back to Employees</a>
</div>
@endsection
