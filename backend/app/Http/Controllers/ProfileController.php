<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MENAMPILKAN PROFIL
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        return view('profile.index', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | MEMPERBARUI INFORMASI PROFIL
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE FOTO PROFIL
    |--------------------------------------------------------------------------
    */

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profile' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto_profile.required' => 'Pilih file gambar terlebih dahulu.',
            'foto_profile.image' => 'File harus berupa gambar.',
            'foto_profile.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'foto_profile.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        $fotoLama = $user->foto_profile;

        try {
            $path = $request->file('foto_profile')->store('profile', 'public');

            $user->foto_profile = $path;
            $user->save();

            if ($fotoLama && Storage::disk('public')->exists($fotoLama)) {
                Storage::disk('public')->delete($fotoLama);
            }

            return back()->with('success', 'Foto profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah foto profil.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS FOTO PROFIL
    |--------------------------------------------------------------------------
    */

    public function destroyFoto()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        if (!$user->foto_profile) {
            return back()->with('error', 'Tidak ada foto profil untuk dihapus.');
        }

        try {
            Storage::disk('public')->delete($user->foto_profile);

            $user->foto_profile = null;
            $user->save();

            return back()->with('success', 'Foto profil berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus foto profil.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE BANNER PROFIL
    |--------------------------------------------------------------------------
    */

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_profile' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'banner_profile.required' => 'Pilih gambar banner terlebih dahulu.',
            'banner_profile.image' => 'File harus berupa gambar.',
            'banner_profile.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'banner_profile.max' => 'Ukuran banner maksimal 4 MB.',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        $bannerLama = $user->banner_profile;

        try {
            $path = $request->file('banner_profile')->store('banners', 'public');

            $user->banner_profile = $path;
            $user->save();

            if ($bannerLama && Storage::disk('public')->exists($bannerLama)) {
                Storage::disk('public')->delete($bannerLama);
            }

            return back()->with('success', 'Banner profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah banner profil.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS BANNER PROFIL
    |--------------------------------------------------------------------------
    */

    public function destroyBanner()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        if (!$user->banner_profile) {
            return back()->with('error', 'Tidak ada banner untuk dihapus.');
        }

        try {
            Storage::disk('public')->delete($user->banner_profile);

            $user->banner_profile = null;
            $user->save();

            return back()->with('success', 'Banner profil berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus banner profil.');
        }
    }
}
