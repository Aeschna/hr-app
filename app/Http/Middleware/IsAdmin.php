<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
{
    if (auth()->check() && auth()->user()->is_admin) {
        
        return $next($request);
    }
    
    
    $user = auth()->user();
    $companyId = $request->route('company'); 

    if ($user->company_id === $companyId) {
       
        return $next($request);
    }

   
    abort(403, 'Unauthorized action.');
}

}

