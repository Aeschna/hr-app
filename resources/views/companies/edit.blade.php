@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Company</h1>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $company->name }}">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $company->address }}">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $company->phone }}">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $company->email }}">
        </div>
        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="file" class="form-control-file" id="logo" name="logo">
            @if($company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" style="width: 100px; height: 100px;">
            @endif
        </div>
        <div class="form-group">
            <label for="website">Website</label>
            <input type="text" class="form-control" id="website" name="website" value="{{ $company->website }}">
        </div>
        
        <!-- Kullanıcı Atama -->
        <div class="form-group">
            <label for="user_id">Assign User</label>
            <select class="form-control" id="user_id" name="user_id">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $user->company_id == $company->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
