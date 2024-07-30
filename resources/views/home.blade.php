@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12"> <!-- Wider column -->
            <!-- Dashboard Card -->
            <div class="card mb-4">
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

            <!-- Welcome Jumbotron -->
            <div class="jumbotron">
                <h1 class="display-4">Welcome, {{ Auth::user()->name }}!</h1>
                
                <hr class="my-4">
               
            </div>
        </div>
    </div>
</div>
@endsection
