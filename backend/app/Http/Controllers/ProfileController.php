<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | UPDATE FOTO PROFIL SENDIRI
    |--------------------------------------------------------------------------
    | Hanya boleh mengganti foto milik user yang sedang login.
    | Field yang dipakai: foto_profile (disk 'public', folder 'profiles').
    */

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profile' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto_profile.required' => 'Pilih file gambar terlebih dahulu.',
            'foto_profile.image'    => 'File yang diunggah harus berupa gambar.',
            'foto_profile.mimes'    => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'foto_profile.max'      => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        $fotoLama = $user->foto_profile;

        try {
            $path = $request->file('foto_profile')->store('profiles', 'public');

            // Hapus file lama setelah file baru berhasil disimpan
            if ($fotoLama && Storage::disk('public')->exists($fotoLama)) {
                Storage::disk('public')->delete($fotoLama);
            }

            $user->foto_profile = $path;
            $user->save();

            return back()->with('success', 'Foto profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah foto profil. Silakan coba lagi.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS FOTO PROFIL SENDIRI
    |--------------------------------------------------------------------------
    | File dihapus dari storage dan field dikembalikan ke NULL (avatar default).
    */

    public function destroyFoto(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        if (!$user->foto_profile) {
            return back()->with('error', 'Tidak ada foto profil untuk dihapus.');
        }

        try {
            if (Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            $user->foto_profile = null;
            $user->save();

            return back()->with('success', 'Foto profil berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus foto profil. Silakan coba lagi.');
        }
    }
}
