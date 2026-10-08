<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Pastikan user yang sedang login status_akun-nya aktif.
     * Jika admin lain menonaktifkan akun ini, request berikutnya akan
     * langsung meng-invalidate session dan mengembalikan user ke login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->isAktif()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
        }

        return $next($request);
    }
}
