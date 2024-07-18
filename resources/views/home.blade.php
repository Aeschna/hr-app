@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
    
    <div class="jumbotron mt-4">
        <h1 class="display-4">Welcome to HR App</h1>
        <p class="lead">This is the home page of your HR application.</p>
        <hr class="my-4">
        <p>You can navigate to different sections using the navigation bar above.</p>
    </div>
</div>
@endsection
