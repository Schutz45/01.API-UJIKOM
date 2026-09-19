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
        $totalAlatRusak             = Pengembalian::where('kondisi_kembali', 'rusak')->count();
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

    public function indexLogAktivitas()
    {
        $logs = LogAktivitas::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.log_aktivitas.index', compact('logs'));
    }

    // CRUD Alat: Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori', 'all');
        $kondisi = $request->input('kondisi', 'all');
        $sort = $request->input('sort', 'terbaru');

        $alats = Alat::with('kategori')

            // Search
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                        ->orWhere('status_kondisi', 'like', "%{$search}%")
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

            // Filter kondisi
            ->when($kondisi !== 'all', function ($query) use ($kondisi) {
                $query->whereRaw(
                    'LOWER(status_kondisi) = ?',
                    [strtolower($kondisi)]
                );
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
            'kondisi',
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
            'status_kondisi'    =>  'required|string|max:100',
            'deskripsi'         =>  'nullable|string',
            'gambar'            =>  'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

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
            'nama_alat'         =>  'required|string|max:255',
            'kategori_id'       =>  'required|exists:kategori,id',
            'stok'              =>  'required|integer|min:0',
            'status_kondisi'    =>  'required|string|max:100',
            'deskripsi'         =>  'nullable|string',
            'gambar'            =>  'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Update Gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            // Hapus Gambar lama jika ada
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file       =   $request->file('gambar');
            $filename   =   time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
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
                $query->whereRaw('LOWER(status_kondisi) = ?', [strtolower($kondisi)]);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                        ->orWhere('status_kondisi', 'like', "%{$search}%");
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
            'tgl_kembali'     => 'required|date',
            'kondisi_kembali' => 'required|in:baik,rusak',
            'denda_kerusakan' => 'nullable|integer|min:0',
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

            // Kembalikan stok hanya jika alat dalam kondisi baik
            if ($request->kondisi_kembali === 'baik') {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
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
            'tgl_kembali'     => 'required|date',
            'kondisi_kembali' => 'required|in:baik,rusak',
            'denda_kerusakan' => 'nullable|integer|min:0',
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

            // Kurangi kembali stok alat
            // karena sebelumnya stok sudah ditambahkan
            // ketika pengembalian diproses.
            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->decrement('stok', $detail->jumlah);
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
