<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        // Validasi email dan password
        $credentials = $request->validate([
            'email' => ['nullable', 'email'],
            'password' => ['nullable'],
        ], [
            'email.required' => 'Email atau password wajib diisi',
            'password.required' => 'Email atau password wajib diisi',
        ]);

        // Jika email atau password kosong
        if (empty($request->email) || empty($request->password)) {
            return back()
                ->withErrors([
                    'login' => 'Email atau password wajib diisi',
                ])
                ->withInput();
        }

        // Cek apakah email sudah terdaftar
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'login' => 'Akun belum terdaftar',
                ])
                ->withInput();
        }

        // Cek email dan password
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            LogAktivitas::create([
                'user_id' => $user->id,
                'aktivitas' => 'Login',
            ]);

            // Redirect berdasarkan Role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.katalog');
            }

            Auth::logout();

            return redirect()->route('login')
                ->with('error', 'Role tidak dikenali');
        }

        // Email terdaftar tetapi password salah
        return back()
            ->withErrors([
                'login' => 'Email atau password salah.',
            ])
            ->withInput();
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

