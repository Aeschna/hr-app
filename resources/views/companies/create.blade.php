@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Company</h1>
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="logo">Logo:</label>
                <input type="file" class="form-control" id="logo" name="logo" required>
            </div>
            <div class="form-group">
                <label for="website">Website:</label>
                <input type="text" class="form-control" id="website" name="website" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
