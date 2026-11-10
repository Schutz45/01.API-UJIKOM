<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan Form Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Memproses Register
    public function register(Request $request)
    {
        // Validasi data pendaftaran
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'foto_profile.image' => 'File harus berupa gambar.',
            'foto_profile.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto_profile.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Upload foto profil jika ada
        $fotoProfile = null;

        if ($request->hasFile('foto_profile')) {
            $fotoProfile = $request->file('foto_profile')
                ->store('profile', 'public');
        }

        // Membuat akun baru
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'peminjam',
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'foto_profile' => $fotoProfile,
        ]);

        // Kembali ke halaman login
        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil. Silakan login menggunakan akun Anda.');
    }


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
            $user = Auth::user();

            // CEK STATUS AKUN
            if (!$user->isAktif()) {
                Auth::logout();
                return back()
                    ->withErrors(['login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.'])
                    ->withInput();
            }

            $request->session()->regenerate();

            LogAktivitas::create([
                'user_id' => $user->id,
                'jenis' => 'auth',
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

