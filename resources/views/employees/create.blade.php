@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Employee</h2>

   

    <form action="{{ route('employees.store') }}" method="POST">
        @csrf

        <!-- First Name -->
        <div class="form-group">
            <label for="first_name">First Name <span class="text-danger">*</span></label>
            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name" value="{{ old('first_name') }}" required>
            @error('first_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="form-group">
            <label for="last_name">Last Name <span class="text-danger">*</span></label>
            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" id="last_name" value="{{ old('last_name') }}" required>
            @error('last_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label for="phone">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Company -->
        <div class="form-group">
            <label for="company_id">Company</label>
            <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" id="company_id" required>
                @if(auth()->user()->is_admin)
                    <!-- If the user is an admin, show all companies -->
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                @else
                    <!-- If the user is not an admin, show only their company -->
                    <option value="{{ auth()->user()->company_id }}" selected>{{ auth()->user()->company->name }}</option>
                @endif
            </select>
            @error('company_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
