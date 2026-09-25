<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // Log Aktivitas
    public function index()
    {
        $totalAlat                  = Alat::count();
        $totalUsers                 = User::count();
        $totalKategori              = Kategori::count();
        $totalPeminjaman            = Peminjaman::count();
        $totalPeminjamanAktif       = Peminjaman::whereIn('status', ['dipinjam', 'telat'])->count();
        $totalPeminjamanSelesai     = Peminjaman::where('status', 'dikembalikan')->count();
        $totalPeminjamanTerlambat   = Peminjaman::where('status', 'telat')->count();
        $totalDenda                 = Pengembalian::sum('denda');
        // Menghitung total unit alat yang rusak dari seluruh data alat
        $totalAlatRusak             = Alat::sum('stok_rusak');
        // Menghitung total unit alat yang tersedia (kondisi baik)
        $totalStokTersedia          = Alat::sum('stok');

        $logs = LogAktivitas::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAlat', 
            'totalUsers', 
            'totalKategori', 
            'totalPeminjaman', 
            'totalPeminjamanAktif',
            'totalPeminjamanSelesai',
            'totalPeminjamanTerlambat',
            'totalDenda',
            'totalAlatRusak',
            'totalStokTersedia',
            'logs'
        ));
    }

    public function indexLogAktivitas(Request $request)
    {
        $search = $request->input('search');
        $userId = $request->input('user_id');
        $jenis  = $request->input('jenis');
        $role   = $request->input('role');
        $dari   = $request->input('dari');
        $sampai = $request->input('sampai');
        $sort   = $request->input('sort', 'terbaru');

        $logs = LogAktivitas::with('user')
            ->when($search, function ($query, $search) {
                $query->where('aktivitas', 'like', "%{$search}%");
            })
            ->when($userId, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($jenis, function ($query, $jenis) {
                $query->where('jenis', $jenis);
            })
            ->when($role, function ($query, $role) {
                $query->whereHas('user', function ($q) use ($role) {
                    $q->where('role', $role);
                });
            })
            ->when($dari, function ($query, $dari) {
                $query->whereDate('created_at', '>=', $dari);
            })
            ->when($sampai, function ($query, $sampai) {
                $query->whereDate('created_at', '<=', $sampai);
            })
            ->when($sort === 'terbaru', function ($query) {
                $query->latest();
            })
            ->when($sort === 'terlama', function ($query) {
                $query->oldest();
            })
            ->paginate(15)
            ->withQueryString();

        // Data untuk dropdown filter pengguna
        $users = User::orderBy('name', 'asc')->get();

        return view('admin.log_aktivitas.index', compact(
            'logs',
            'users',
            'search',
            'userId',
            'jenis',
            'role',
            'dari',
            'sampai',
            'sort'
        ));
    }

    // CRUD Alat: Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori', 'all');
        $sort = $request->input('sort', 'terbaru');

        $alats = Alat::with('kategori')

            // Search nama alat dan kategori
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                        ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                            $kategoriQuery->where(
                                'nama_kategori',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })

            // Filter kategori
            ->when($kategori !== 'all', function ($query) use ($kategori) {
                $query->where('kategori_id', $kategori);
            })

            // Sorting
            ->when($sort === 'terbaru', function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($sort === 'terlama', function ($query) {
                $query->orderBy('created_at', 'asc');
            })

            ->when($sort === 'az', function ($query) {
                $query->orderBy('nama_alat', 'asc');
            })

            ->when($sort === 'za', function ($query) {
                $query->orderBy('nama_alat', 'desc');
            })

            ->paginate(10)
            ->withQueryString();

        // Data untuk dropdown kategori
        $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.alat.index', compact(
            'alats',
            'kategoris',
            'search',
            'kategori',
            'sort'
        ));
    }

    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    // Menyimpan Alat Baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat'         =>  'required|string|max:255',
            'kategori_id'       =>  'required|exists:kategori,id',
            'stok'              =>  'required|integer|min:0',
            'stok_rusak'        =>  'nullable|integer|min:0',
            'deskripsi'         =>  'nullable|string',
            'gambar'            =>  'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['nama_alat', 'kategori_id', 'stok', 'stok_rusak', 'deskripsi']);
        $data['stok_rusak'] = $data['stok_rusak'] ?? 0;

        // Handle Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            $file       =   $request->file('gambar');
            $filename   =   time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    // Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat       =   Alat::findOrFail($id);
        $kategoris  =   Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'deskripsi'      => 'nullable|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $alat->nama_alat     = $request->nama_alat;
        $alat->kategori_id   = $request->kategori_id;
        $alat->deskripsi     = $request->deskripsi;

        if ($request->hasFile('gambar')) {
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }
            $file = $request->file('gambar');
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $alat->gambar = 'storage/alat/' . $filename;
        }

        $alat->save();

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    /**
     * Menandai sejumlah unit alat sebagai rusak.
     * Stok (baik) berkurang, stok_rusak bertambah.
     */
    public function tandaiRusakAlat(Request $request, Alat $alat)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $jumlah = (int) $request->jumlah;

        if ($jumlah > $alat->stok) {
            return back()->with('error', 'Jumlah alat yang ditandai rusak tidak boleh melebihi stok alat baik.');
        }

        DB::beginTransaction();

        try {
            $alat->decrement('stok', $jumlah);
            $alat->increment('stok_rusak', $jumlah);
            $alat->syncStatusKondisi();

            DB::commit();

            return redirect()
                ->route('admin.alat.edit', $alat->id)
                ->with('success', $jumlah . ' unit ' . $alat->nama_alat . ' berhasil ditandai rusak.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Gagal menandai alat rusak: ' . $e->getMessage());
        }
    }

    public function perbaikiAlat(Request $request, Alat $alat)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $jumlah = (int) $request->jumlah;

        if ($jumlah > $alat->stok_rusak) {
            return back()->with('error', 'Jumlah alat yang diperbaiki tidak boleh melebihi stok alat rusak.');
        }

        DB::beginTransaction();

        try {
            $alat->decrement('stok_rusak', $jumlah);
            $alat->increment('stok', $jumlah);
            $alat->syncStatusKondisi();

            DB::commit();

            return redirect()
                ->route('admin.alat.edit', $alat->id)
                ->with('success', $jumlah . ' unit ' . $alat->nama_alat . ' berhasil diperbaiki dan dikembalikan ke stok tersedia.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Gagal memperbaiki alat: ' . $e->getMessage());
        }
    }

    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | CEK RIWAYAT PEMINJAMAN
        |--------------------------------------------------------------------------
        | Alat yang sudah pernah dipinjam tidak boleh dihapus karena
        | data tersebut masih digunakan dalam riwayat peminjaman.
        */
        if ($alat->detailPinjam()->exists()) {
            return redirect()
                ->route('admin.alat.index')
                ->with(
                    'error',
                    'Alat tidak dapat dihapus karena sudah memiliki riwayat peminjaman.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CEK STOK RUSAK
        |--------------------------------------------------------------------------
        | Jika masih ada unit rusak, proses perbaikan harus diselesaikan
        | terlebih dahulu melalui halaman Alat Rusak / Perbaikan.
        */
        if ($alat->stok_rusak > 0) {
            return redirect()
                ->route('admin.alat.index')
                ->with(
                    'error',
                    'Alat tidak dapat dihapus karena masih memiliki '
                    . $alat->stok_rusak
                    . ' unit alat rusak. Silakan proses melalui halaman Alat Rusak / Perbaikan terlebih dahulu.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PATH GAMBAR
        |--------------------------------------------------------------------------
        | Gambar baru dihapus setelah data alat berhasil dihapus.
        */
        $gambar = $alat->gambar;

        /*
        |--------------------------------------------------------------------------
        | HAPUS DATA ALAT
        |--------------------------------------------------------------------------
        */
        $alat->delete();

        /*
        |--------------------------------------------------------------------------
        | HAPUS FILE GAMBAR
        |--------------------------------------------------------------------------
        */
        if ($gambar && file_exists(public_path($gambar))) {
            unlink(public_path($gambar));
        }

        return redirect()
            ->route('admin.alat.index')
            ->with(
                'success',
                'Data alat "' . $alat->nama_alat . '" berhasil dihapus.'
            );
    }

    // CRUD User (Manajemen User Admin, Petugas, Peminjam)
    public function indexUser(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'all');
        $sort = $request->input('sort', 'terbaru');

        $users = User::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        })
            ->when($role !== 'all', function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->when($sort === 'terbaru', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($sort === 'terlama', function ($query) {
                $query->orderBy('created_at', 'asc');
            })
            ->when($sort === 'az', function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->when($sort === 'za', function ($query) {
                $query->orderBy('name', 'desc');
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.user.index', compact(
            'users',
            'search',
            'role',
            'sort'
        ));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    // Menyimpan User Baru ke Database
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:admin,petugas,peminjam',
            'no_hp'        => 'nullable|string|max:20',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak sesuai.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $fotoProfile = null;

        if ($request->hasFile('foto_profile')) {
            $fotoProfile = $request->file('foto_profile')
                ->store('profile', 'public');
        }

        User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'no_hp'        => $request->no_hp,
            'foto_profile' => $fotoProfile,
        ]);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    // Memperbarui Data User
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'role'         => 'required|in:admin,petugas,peminjam',
            'no_hp'        => 'nullable|string|max:20',
            'password'     => 'nullable|string|min:6',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak sesuai.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'no_hp' => $request->no_hp,
        ];

        // Jika password diisi, password diperbarui
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Jika user memilih foto baru
        if ($request->hasFile('foto_profile')) {

            // Hapus foto lama jika ada
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            // Simpan foto baru
            $data['foto_profile'] = $request->file('foto_profile')
                ->store('profile', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'terbaru');

        $kategoris = Kategori::withCount('alat')
            ->when($search, function ($query, $search) {
                return $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->when($sort === 'terbaru', function ($query) {
                return $query->orderBy('created_at', 'desc');
            })
            ->when($sort === 'terlama', function ($query) {
                return $query->orderBy('created_at', 'asc');
            })
            ->when($sort === 'az', function ($query) {
                return $query->orderBy('nama_kategori', 'asc');
            })
            ->when($sort === 'za', function ($query) {
                return $query->orderBy('nama_kategori', 'desc');
            })
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact(
            'kategoris',
            'search',
            'sort'
        ));
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' =>  'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' =>  $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' =>  'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
             'nama_kategori' =>  $request->nama_kategori, 
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // 6. Menghapus kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Opsional: Cek apakah kategori masih dipakai oleh alat
        if ($kategori->alat()->count() > 0) {
            return redirect()->route('admin.kategori.index')
            ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function alatKategori(Request $request, Kategori $kategori)
    {
        $search = $request->input('search');
        $kondisi = $request->input('kondisi');

        $alats = $kategori->alat()
            ->when($kondisi, function ($query, $kondisi) {
                // status_kondisi sekarang computed dari stok & stok_rusak
                $kondisi = strtolower($kondisi);
                if ($kondisi === 'baik') {
                    $query->where('stok', '>', 0)->where('stok_rusak', 0);
                } elseif ($kondisi === 'sebagian rusak') {
                    $query->where('stok', '>', 0)->where('stok_rusak', '>', 0);
                } elseif ($kondisi === 'rusak') {
                    $query->where('stok', 0)->where('stok_rusak', '>', 0);
                }
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                        ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                            $kategoriQuery->where('nama_kategori', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori.alat', compact(
            'kategori',
            'alats',
            'search',
            'kondisi'
        ));
    }

    // 1. Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sort = $request->input('sort', 'terbaru');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            // Filter status
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })

            // Search nama peminjam / status
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            })

            // Urutan data
            ->when($sort === 'terbaru', function ($query) {
                $query->latest();
            })
            ->when($sort === 'terlama', function ($query) {
                $query->oldest();
            })
            ->when($sort === 'az', function ($query) {
                $query->orderBy(
                    User::select('name')
                        ->whereColumn('users.id', 'peminjamans.user_id'),
                    'asc'
                );
            })
            ->when($sort === 'za', function ($query) {
                $query->orderBy(
                    User::select('name')
                        ->whereColumn('users.id', 'peminjamans.user_id'),
                    'desc'
                );
            })

            ->paginate()
            ->withQueryString();

        return view('admin.peminjaman.index', compact(
            'peminjamans',
            'search',
            'status',
            'sort'
        ));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get(); // Atau ambil semua user jika bebas
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id'           =>  'required|exists:users,id',
            'tgl_pinjam'        =>  'required|date',
            'tgl_kembali_plan'  =>  'required|date|after_or_equal:tgl_pinjam',
            'alat_id'           =>  'required|array',
            'alat_id.*'         =>  'exists:alat,id',
            'jumlah'            =>  'required|array',
            'jumlah.*'          =>  'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'           =>  $request->user_id,
                'tgl_pinjam'        =>  $request->tgl_pinjam,
                'tgl_kembali_plan'  =>  $request->tgl_kembali_plan,
                'status'            =>  'diajukan', // Status awal
            ]);

            // Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam   =   $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                // Validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' =>  $peminjaman->id,
                    'alat_id'       =>  $alatId,
                    'jumlah'        =>  $jumlahPinjam,
                ]);

                // Kurangi stok alat jika status langsung disetujui/dipinjam (opsional, atau dikurangi saat status berubah jadi 'dipinjam')
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman (Misal: dari diajukan -> dipinjam / selesai)
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,telat',
        ]);

        DB::beginTransaction();

        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            // Jika status berubah menjadi dipinjam,
            // kurangi stok alat.
            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {

                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = $detail->alat;

                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception(
                            "Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam."
                        );
                    }

                    $alat->decrement('stok', $detail->jumlah);
                    $alat->syncStatusKondisi();
                }
            }

            $peminjaman->update([
                'status' => $statusBaru
            ]);

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with('success', 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

        // Jika statusnya sedang dipinjam, kembalikan stok terlebih dahulu sebelum dihapus
        if (in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
                $detail->alat->syncStatusKondisi();
            }
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');    
    }

    // Pengembalian
    public function createPengembalian($id)
    {
        $peminjaman =   Peminjaman::with([
            'user',
            'detailPinjam.alat'
        ])->findOrFail($id);

        // Peminjaman yang sedang dipinjam atau terlambat yang dapat diproses sebagai pengembalian.
        if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            return redirect()
                ->route('admin.peminjaman.index')
                ->with('error', 'Peminjaman ini belum dapat diproses sebagai pengembalian.');
        }

        return view('admin.pengembalian.create', compact('peminjaman'));
    }

    // Menyimpan data pengembalian
    public function storePengembalian(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        // Pastikan hanya peminjaman yang sedang dipinjam/telat yang dapat diproses sebagai pengembalian.
        if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
            return redirect()
                ->route('admin.peminjaman.index')
                ->with('error', 'Peminjaman ini tidak dapat diproses sebagai pengembalian.');
        }

        $request->validate([
            'tgl_kembali'       => 'required|date',
            'kondisi_kembali'   => 'required|in:baik,rusak',
            'denda_kerusakan'   => 'nullable|integer|min:0',
            'jumlah_rusak'      => 'nullable|array',
            'jumlah_rusak.*'    => 'nullable|integer|min:0',
        ]);

        // Pastikan tanggal kembali tidak sebelum tanggal pinjam
        if ($request->tgl_kembali < $peminjaman->tgl_pinjam->format('Y-m-d')) {
            return back()
                ->withInput()
                ->withErrors([
                    'tgl_kembali' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.'
                ]);
        }

        DB::beginTransaction();

        try {
            // Hitung jumlah hari keterlambatan
            $tglKembali = \Carbon\Carbon::parse($request->tgl_kembali);
            $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);

            $hariTelat = 0;

            if ($tglKembali->greaterThan($tglRencana)) {
                $hariTelat = $tglRencana->diffInDays($tglKembali);
            }

            // Denda keterlambatan Rp1.000 per hari
            $dendaKeterlambatan = $hariTelat * 1000;

            // Denda kerusakan dari input admin
            $dendaKerusakan = 0;

            if ($request->kondisi_kembali === 'rusak') {
                $dendaKerusakan = (int) ($request->denda_kerusakan ?? 0);
            }

            // Total denda
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id'       => $peminjaman->id,
                'tgl_kembali'         => $request->tgl_kembali,
                'kondisi_kembali'     => $request->kondisi_kembali,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan'     => $dendaKerusakan,
                'denda'               => $totalDenda,
                'petugas_id'          => Auth::id(),
            ]);

            // Kembalikan stok alat berdasarkan kondisi pengembalian
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;

                if (!$alat) {
                    continue;
                }

                $jumlahRusak = 0;

                if ($request->kondisi_kembali === 'rusak') {
                    $inputRusak = $request->jumlah_rusak[$detail->alat_id] ?? null;

                    // POIN 3: kosong = seluruh unit dianggap rusak
                    // POIN 4: diisi = hanya sejumlah nominal itu yang rusak
                    $jumlahRusak = ($inputRusak === null || $inputRusak === '')
                        ? $detail->jumlah
                        : (int) $inputRusak;

                    // POIN 5: jumlah rusak tidak boleh melebihi jumlah yang dipinjam
                    if ($jumlahRusak > $detail->jumlah) {
                        throw new \Exception(
                            "Jumlah alat rusak untuk '{$alat->nama_alat}' ({$jumlahRusak}) melebihi jumlah yang dipinjam ({$detail->jumlah})."
                        );
                    }
                }

                $jumlahBaik = $detail->jumlah - $jumlahRusak;

                // POIN 7: simpan jumlah rusak per detail alat
                $detail->update(['jumlah_rusak' => $jumlahRusak]);

                // POIN 6: alat baik & rusak terpisah dengan benar
                $alat->increment('stok', $jumlahBaik);
                $alat->increment('stok_rusak', $jumlahRusak);
                $alat->syncStatusKondisi();

                if ($jumlahRusak > 0) {
                    // TAHAP 2: Notifikasi otomatis ke Admin (alat rusak baru)
                    \App\Services\NotifikasiService::alatRusakBaru($alat, $jumlahRusak);
                }
            }

            // Ubah status peminjaman menjadi dikembalikan
            $peminjaman->update([
                'status' => 'dikembalikan'
            ]);

            DB::commit();

            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'success',
                    'Pengembalian berhasil diproses. Total denda: Rp' .
                    number_format($totalDenda, 0, ',', '.')
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memproses pengembalian: ' . $e->getMessage()
                );
        }
    }

    // Menampilkan riwayat pengembalian
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');
        $kondisi = $request->input('kondisi');
        $sort = $request->input('sort', 'terbaru');

        $pengembalians = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ])
            // Filter kondisi
            ->when($kondisi, function ($query, $kondisi) {
                $query->where('kondisi_kembali', $kondisi);
            })

            // Search nama peminjam / kondisi
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kondisi_kembali', 'like', "%{$search}%")
                        ->orWhereHas('peminjaman.user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            })

            // Urutan
            ->when($sort === 'terbaru', function ($query) {
                $query->latest();
            })
            ->when($sort === 'terlama', function ($query) {
                $query->oldest();
            })
            ->when($sort === 'az', function ($query) {
                $query->orderBy(
                    User::select('name')
                        ->whereColumn(
                            'users.id',
                            'peminjamans.user_id'
                        ),
                    'asc'
                );
            })
            ->when($sort === 'za', function ($query) {
                $query->orderBy(
                    User::select('name')
                        ->whereColumn(
                            'users.id',
                            'peminjamans.user_id'
                        ),
                    'desc'
                );
            })

            ->paginate()
            ->withQueryString();

        return view('admin.pengembalian.index', compact(
            'pengembalians',
            'search',
            'kondisi',
            'sort'
        ));
    }

    public function editPengembalian($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.detailPinjam.alat',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ])->findOrFail($id);

        return view('admin.pengembalian.edit', compact('pengembalian'));
    }    

    public function updatePengembalian(Request $request, $id)
    {
        $pengembalian = Pengembalian::with(
        'peminjaman.detailPinjam.alat'
        )->findOrFail($id);

        $peminjaman = $pengembalian->peminjaman;

        $request->validate([
            'tgl_kembali'       => 'required|date',
            'kondisi_kembali'   => 'required|in:baik,rusak',
            'denda_kerusakan'   => 'nullable|integer|min:0',
            'jumlah_rusak'      => 'nullable|array',
            'jumlah_rusak.*'    => 'nullable|integer|min:0',
        ]);

        // Pastikan tanggal kembali tidak sebelum tanggal pinjam
        if ($request->tgl_kembali < $peminjaman->tgl_pinjam->format('Y-m-d')) {
            return back()
                ->withInput()
                ->withErrors([
                    'tgl_kembali' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.'
                ]);
        }

        DB::beginTransaction();

        try {

            // Hitung ulang jumlah hari keterlambatan
            $tglKembali = \Carbon\Carbon::parse($request->tgl_kembali);
            $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);

            $hariTelat = 0;

            if ($tglKembali->greaterThan($tglRencana)) {
                $hariTelat = $tglRencana->diffInDays($tglKembali);
            }

            // Denda keterlambatan Rp1.000 per hari
            $dendaKeterlambatan = $hariTelat * 1000;

            // Denda kerusakan
            $dendaKerusakan = 0;

            if ($request->kondisi_kembali === 'rusak') {
                $dendaKerusakan = (int) ($request->denda_kerusakan ?? 0);
            }

            // Total denda
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            /*
            |----------------------------------------------------------------------
            | POIN 8: Batalkan pencatatan stok sebelumnya, terapkan pencatatan baru
            |----------------------------------------------------------------------
            | Stok harus selalu dihitung ulang dari nol agar tidak dobel.
            |----------------------------------------------------------------------
            */
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;

                if (!$alat) {
                    continue;
                }

                // 1. Batalkan pencatatan lama (dari jumlah_rusak yang tersimpan)
                $rusakLama = (int) ($detail->jumlah_rusak ?? 0);
                $baikLama  = $detail->jumlah - $rusakLama;

                $alat->decrement('stok', $baikLama);
                $alat->decrement('stok_rusak', $rusakLama);
                $alat->syncStatusKondisi();

                // 2. Hitung pencatatan baru
                $rusakBaru = 0;

                if ($request->kondisi_kembali === 'rusak') {
                    $inputRusak = $request->jumlah_rusak[$detail->alat_id] ?? null;

                    $rusakBaru = ($inputRusak === null || $inputRusak === '')
                        ? $detail->jumlah
                        : (int) $inputRusak;

                    if ($rusakBaru > $detail->jumlah) {
                        throw new \Exception(
                            "Jumlah alat rusak untuk '{$alat->nama_alat}' ({$rusakBaru}) melebihi jumlah yang dipinjam ({$detail->jumlah})."
                        );
                    }
                }

                $baikBaru = $detail->jumlah - $rusakBaru;

                // 3. Terapkan pencatatan baru
                $alat->increment('stok', $baikBaru);
                $alat->increment('stok_rusak', $rusakBaru);
                $alat->syncStatusKondisi();

                // 4. Perbarui jumlah_rusak disimpan per detail alat
                $detail->update(['jumlah_rusak' => $rusakBaru]);
            }

            // Update data pengembalian
            $pengembalian->update([
                'tgl_kembali'         => $request->tgl_kembali,
                'kondisi_kembali'     => $request->kondisi_kembali,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan'     => $dendaKerusakan,
                'denda'               => $totalDenda,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Data pengembalian berhasil diperbarui. Total denda: Rp' .
                    number_format($totalDenda, 0, ',', '.')
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memperbarui pengembalian: ' . $e->getMessage()
                );
        }


    }

    public function destroyPengembalian($id)
    {
        $pengembalian = Pengembalian::with(
            'peminjaman.detailPinjam.alat'
        )->findOrFail($id);

        $peminjaman = $pengembalian->peminjaman;

        DB::beginTransaction();

        try {

            // POIN 9: Kembalikan kondisi stok seperti sebelum pengembalian diproses
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;
                if ($alat) {
                    $rusakDicatat = (int) ($detail->jumlah_rusak ?? 0);
                    $baikDicatat  = $detail->jumlah - $rusakDicatat;

                    // Kurangi stok dari pencatatan pengembalian
                    $alat->decrement('stok', $baikDicatat);
                    $alat->decrement('stok_rusak', $rusakDicatat);
                    $alat->syncStatusKondisi();

                    // Reset jumlah_rusak di detail pinjam
                    $detail->update(['jumlah_rusak' => 0]);
                }
            }

            // Kembalikan status peminjaman
            $peminjaman->update([
                'status' => 'dipinjam'
            ]);

            // Hapus data pengembalian
            $pengembalian->delete();

            DB::commit();

            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Data pengembalian berhasil dihapus dan status peminjaman dikembalikan menjadi dipinjam.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'Gagal menghapus pengembalian: ' . $e->getMessage()
                );
        }
    }



}
