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
        // Admin ise, devam et
        return $next($request);
    }
    
    // Şirket yöneticisi olup olmadığını kontrol et
    $user = auth()->user();
    $companyId = $request->route('company'); // Eğer route'dan şirket ID'si alıyorsanız

    if ($user->company_id === $companyId) {
        // Şirket yöneticisi ise, devam et
        return $next($request);
    }

    // Aksi takdirde erişim engelle
    abort(403, 'Unauthorized action.');
}

}

