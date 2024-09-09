@extends('layouts.app')

@section('content')
<head>
@vite('resources/css/app.css')
</head>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Dashboard Card -->
            <div class="card mb-4">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if(Auth::check())
                        <h4>Welcome, {{ Auth::user()->name }}!</h4>
                    @else
                        <h4>Welcome!</h4>
                    @endif
                    <p>{{ __('You are logged in!') }}</p>
                </div>
            </div>

            <!-- Statistics -->
<div class="row mt-4">
    <!-- Companies Count -->
    <div class="col-md-3">
        <div class="card bg-primary text-white mb-3">
            <div class="card-header">Companies</div>
            <div class="card-body">
                <h2 class="card-title">{{ $companyCount }}</h2>
            </div>
        </div>
    </div>

    <!-- Users Count -->
    <div class="col-md-3">
        <div class="card bg-success text-white mb-3">
            <div class="card-header">Users</div>
            <div class="card-body">
                <h2 class="card-title">{{ $userCount }}</h2>
            </div>
        </div>
    </div>

    <!-- Employees Count -->
    <div class="col-md-3">
        <div class="card bg-info text-white mb-3">
            <div class="card-header">Employees</div>
            <div class="card-body">
                <h2 class="card-title">{{ $employeeCount }}</h2>
            </div>
        </div>
    </div>

    <!-- Your Employees -->
    <div class="col-md-3">
        <div class="card bg-warning text-white mb-3">
            <div class="card-header">Your Employees</div>
            <div class="card-body">
                <h2 class="card-title">{{ $userEmployeeCount }}</h2>
            </div>
        </div>
    </div>
</div>


            <!-- Recent Activities -->
            <div class="card mb-4">
                <div class="card-header">{{ __('Recent Activities') }}</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($recentActivities as $activity)
                            <li class="list-group-item">
                                <strong>{{ $activity->type }}:</strong> {{ $activity->model_type }} ID: {{ $activity->model_id }}
                                <span class="badge bg-{{ $activity->type === 'Deleted' ? 'danger' : ($activity->type === 'Restored' ? 'success' : 'primary') }} float-end">{{ $activity->type }}</span>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
<!-- Vue -->
<div id="app"></div>

@vite('resources/js/app.js')



            <!-- Alerts -->
            <div class="card mb-4">
                <div class="card-header">{{ __('Alerts') }}</div>
                <div class="card-body">
                    @if($alertCount > 0)
                        <div class="alert alert-warning" role="alert">
                            There are {{ $alertCount }} pending alerts.
                        </div>
                    @else
                        <div class="alert alert-success" role="alert">
                            No pending alerts.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
