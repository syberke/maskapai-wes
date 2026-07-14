<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Memeriksa apakah role user saat ini diizinkan mengakses route ini
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika tidak berhak, arahkan balik dengan pesan error
        abort(403, 'Akses Ditolak. Anda tidak memiliki wewenang untuk halaman ini.');
    }
}