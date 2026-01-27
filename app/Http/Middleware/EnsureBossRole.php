<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBossRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Cek apakah user adalah Boss
        if (!auth()->user()->isBoss()) {
            abort(403, 'Akses ditolak. Hanya Boss yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}