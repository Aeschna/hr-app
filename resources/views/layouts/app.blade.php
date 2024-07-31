<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR App</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Place additional stylesheets or scripts here -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
    <a class="navbar-brand" href="{{ url('/') }}" style="font-size: 1.5rem;">
        <img src="{{ asset('storage/logos/Hr App.png') }}" style="height: 40px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @auth
                @if(auth()->user()->is_admin || (auth()->user()->company_id == request()->route('company')))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('companies.index') }}">Companies</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('employees.index') }}">Employees</a>
                </li>
                <!-- Add more navigation items as needed -->
            @endauth
        </ul>

        <!-- Right-side navigation items -->
        <ul class="navbar-nav ml-auto">
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
            @else
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Logout</button>
                    </form>
                </li>
            @endguest
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <!-- Breadcrumb -->
    @if (!request()->routeIs('login'))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @if (request()->routeIs('companies.*'))
                    <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
                    @if (request()->routeIs('companies.create'))
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    @elseif (request()->routeIs('companies.edit'))
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    @elseif (request()->routeIs('companies.show'))
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    @endif
                @elseif (request()->routeIs('employees.*'))
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                    @if (request()->routeIs('employees.create'))
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    @elseif (request()->routeIs('employees.edit'))
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    @elseif (request()->routeIs('employees.show'))
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    @endif
                @endif


              
                <!-- Additional breadcrumb items can be added as needed -->
            </ol>
        </nav>
    @endif

    <!-- Notification message -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @yield('content')
</div>

<!-- Optional: JavaScript files -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Place additional scripts here -->
</body>
</html>
