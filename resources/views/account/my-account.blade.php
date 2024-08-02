@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Account</h1>

    <!-- User Info Section -->
    <div class="card mb-4">
        <div class="card-header">Account Information</div>
        <div class="card-body">
            <!-- Display user information here, e.g., name, email -->
            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        </div>
    </div>

    <!-- Password Change Section -->
    <div class="card">
        <div class="card-header">Change Password</div>
        <div class="card-body">
            <form action="{{ route('account.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
