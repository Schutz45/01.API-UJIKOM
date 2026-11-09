# Rancangan Sistem Notifikasi SIPPSD

> **Prinsip utama:**
> - 🔔 **Lonceng** = *"Ada informasi baru."*
> - 🔴 **Badge fitur** = *"Ada pekerjaan yang harus saya lakukan."*

---

## 1. Dua Lapisan Notifikasi

### A. Lonceng (Bell)
Pusat informasi aktivitas. Menampilkan **jumlah notifikasi yang belum dibaca**.

Isi mencakup:
- Pengajuan peminjaman baru
- Peminjaman disetujui / ditolak
- Pengembalian diproses
- Pengembalian selesai
- Informasi denda
- Informasi lain yang relevan

Contoh: `🔔 3` → ada 3 notifikasi belum dibaca.

### B. Badge pada Fitur (Action Badge)
Indikator pekerjaan yang **membutuhkan tindakan**.

Contoh: `Peminjaman 🔴 3` → ada 3 pengajuan yang harus diproses.

> Angka pada badge **bukan** jumlah notifikasi, melainkan jumlah pekerjaan yang masih perlu ditangani.

---

## 2. Kategori Notifikasi

| Kategori | Butuh Tindakan? | Tampil di | Contoh |
|----------|-----------------|-----------|--------|
| **Notifikasi Tindakan** | ✅ Ya | Lonceng **+** Badge fitur | Pengajuan menunggu persetujuan, pengembalian menunggu pemeriksaan, alat rusak perlu ditangani |
| **Notifikasi Informasi** | ❌ Tidak | Lonceng saja | Peminjaman disetujui/ditolak, pengembalian selesai, denda tercatat |

---

## 3. Alur Pengajuan Peminjaman

```
Peminjam ──Ajukan peminjaman──► Sistem
                                 │
                                 ├─ Simpan pengajuan
                                 ├─ Buat notifikasi
                                 │    ├─► 🔔 Lonceng Petugas  +1
                                 │    └─► 🔴 Peminjaman (Petugas) +1
                                 ▼
                              Petugas melihat:
                              Peminjaman 🔴 1
                                 │
                              Buka pengajuan:
                              ┌──────────────────────────┐
                              │ Pengajuan Peminjaman      │
                              │ Budi - Laptop Lenovo      │
                              │ Tanggal: ...  Status: Diajukan │
                              │ [Setujui]  [Tolak]        │
                              └──────────────────────────┘
```

### Setelah petugas menyetujui
```
Petugas ──Setujui──► Sistem
                      ├─ Status → dipinjam
                      ├─ Stok berkurang
                      └─ Notifikasi peminjam
                           └─► 🔔 Lonceng Peminjam +1
                               "Peminjaman Anda telah disetujui."

❌ Tidak ada badge merah pada fitur peminjam
   (peminjam tidak punya pekerjaan yang harus dilakukan)
```

### Jika pengajuan ditolak
```
Petugas ──Tolak──► Sistem
                    └─ Notifikasi peminjam
                         └─► 🔔 Lonceng Peminjam +1
                             "Peminjaman Anda ditolak."

❌ Tidak ada badge (informasi, bukan pekerjaan)
```

---

## 4. Alur Pengembalian

```
Peminjam ──Pengembalian──► Sistem
                           ├─ Notifikasi Petugas
                           │    ├─► 🔔 Lonceng Petugas +1
                           │    └─► 🔴 Pengembalian (Petugas) +1
                           ▼
                        Petugas melihat:
                        Pengembalian 🔴 1
                           │
                        Masuk halaman → pemeriksaan
                           │
                     ┌─────┴─────┐
                     ▼           ▼
                   Baik        Rusak
                              Jumlah rusak: 2
                     │           │
                  stok ↑     stok_rusak ↑
                     │           │
                     └─────┬─────┘
                           ▼
                    Pengembalian selesai
                           │
                           ├─► 🔔 Lonceng Peminjam +1
                           │   "Pengembalian Anda telah diproses."
                           │
                           └─► 🔴 Badge Pengembalian (Petugas) -1
                               (pekerjaan selesai)
```

---

## 5. Alur Alat Rusak (khusus Admin)

Jika pengembalian menyebabkan alat masuk `stok_rusak`:

```
Pengembalian → 2 unit rusak → stok_rusak +2 → masuk Alat Rusak
                                                      │
                                                      ▼
                                          Admin melihat:
                                          Alat Rusak 🔴 1
```

### ⚠️ Penentuan makna angka badge Alat Rusak
Badge menunjukkan **jumlah data/jenis alat** yang perlu ditangani, **bukan jumlah unit**.

Contoh:
- `Alat Rusak 🔴 1` → ada **1 jenis alat** bermasalah
- Rincian: Kamera DSLR — stok rusak: 10 unit

> Ini mencegah badge `🔴 10` disalahartikan sebagai 10 pekerjaan berbeda.
> Padahal hanya 1 jenis alat yang perlu ditangani (bisa diperbaiki sekaligus).

---

## 6. Lonceng ≠ Badge (prinsip penting)

Kedua angka memiliki makna berbeda dan **tidak harus sama**.

| Komponen | Makna |
|----------|-------|
| `🔔 5` | Jumlah notifikasi yang belum dibaca |
| `Peminjaman 🔴 2` | Jumlah pengajuan yang masih perlu diproses |
| `Pengembalian 🔴 1` | Jumlah pengembalian yang masih perlu diperiksa |

Contoh: Lonceng 5, tetapi Peminjaman 2 + Pengembalian 1 = wajar, karena sebagian notifikasi adalah informasi (disetujui/ditolak/selesai) yang tidak menambah badge.

---

## 7. Target Tampilan per Role

### Petugas
```
Dashboard Petugas              🔔 3

  Peminjaman 🔴 2
  Pengembalian 🔴 1
```
→ "Ada 2 pengajuan dan 1 pengembalian yang harus saya tangani."

### Peminjam
```
Dashboard Peminjam             🔔 2

  Peminjaman
  Riwayat Peminjaman
```
Lonceng berisi:
- ✅ Peminjaman Anda disetujui
- ✅ Pengembalian Anda telah diproses

❌ Tidak perlu badge merah di menu peminjam (tidak ada tindakan).

### Admin
```
Dashboard Admin               🔔 1

  Alat Rusak 🔴 1
```
→ "Ada data alat rusak yang perlu diperhatikan."

---

## 8. Struktur Pekerjaan Implementasi (5 Tahap)

### Tahap 1 — Database
Buat tabel/model notifikasi. Kolom yang disimpan:

| Kolom | Keterangan |
|-------|------------|
| `id` | Primary key |
| `user_id` | Penerima notifikasi |
| `jenis` | Tindakan / Informasi |
| `judul` | Judul singkat |
| `pesan` | Isi pesan |
| `dibaca` | boolean (sudah/belum dibaca) |
| `referensi_tipe` | Model terkait (mis. peminjaman, pengembalian, alat) |
| `referensi_id` | ID data terkait |
| `url_tujuan` | Link langsung ke halaman aksi |
| `created_at` | Waktu notifikasi |

### Tahap 2 — Backend (otomatis saat aktivitas terjadi)
| Trigger | Notifikasi ke |
|---------|--------------|
| Pengajuan peminjaman | Petugas (tindakan) |
| Persetujuan peminjaman | Peminjam (informasi) |
| Penolakan peminjaman | Peminjam (informasi) |
| Pengajuan pengembalian | Petugas (tindakan) |
| Pengembalian selesai | Peminjam (informasi) |
| Alat masuk stok_rusak | Admin (tindakan) |

### Tahap 3 — Lonceng
- Hitung jumlah `dibaca = false` untuk user yang login.
- Tampilkan daftar notifikasi (dropdown/panel).

### Tahap 4 — Badge Fitur
Counter khusus berdasarkan pekerjaan yang masih perlu tindakan:
- `Peminjaman 🔴 N` = jumlah pengajuan berstatus `diajukan`
- `Pengembalian 🔴 N` = jumlah pengembalian menunggu pemeriksaan
- `Alat Rusak 🔴 N` = jumlah **jenis alat** dengan `stok_rusak > 0`

> Bukan sekadar jumlah notifikasi.

### Tahap 5 — Tautan Langsung
Klik notifikasi/badge → langsung ke halaman relevan, bukan sekadar daftar pesan.

```
Peminjaman 🔴 3  ──klik──►  Halaman pengajuan peminjaman
```

---

## 9. Kesepakatan Konsep

| Komponen | Definisi |
|----------|----------|
| 🔔 Lonceng | "Ada informasi baru." |
| 🔴 Badge fitur | "Ada pekerjaan yang harus saya lakukan." |

Dengan pola ini, notifikasi bukan hiasan navbar — ia benar-benar membantu:
- **Petugas/Admin** menemukan pekerjaan yang harus dikerjakan.
- **Peminjam** mengetahui perkembangan transaksinya.
