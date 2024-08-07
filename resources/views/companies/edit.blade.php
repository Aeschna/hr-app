@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Company</h1>

    

    <form action="{{ route('companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="form-group">
            <label for="name">Name: <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}">
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Address -->
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $company->address) }}">
            @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
            @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email) }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Logo -->
        <div class="form-group">
            <label for="logo">Logo:</label>
            <input type="file" class="form-control-file @error('logo') is-invalid @enderror" id="logo" name="logo">
            @if($company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" style="width: 100px; height: 100px;">
            @endif
            @error('logo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Website -->
        <div class="form-group">
            <label for="website">Website:</label>
            <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website) }}">
            @error('website')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Assign User -->
        <div class="form-group">
            <label for="user_id">Assign User: <span class="text-danger">*</span></label>
            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $user->company_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
