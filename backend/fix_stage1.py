import pathlib

p = pathlib.Path('app/Http/Controllers/AdminController.php')
src = p.read_text(encoding='utf-8')

old_val = "'status' => 'required|in:diajukan,dipinjam,telat,dikembalikan',"
new_val = "'status' => 'required|in:diajukan,dipinjam,telat',"

if old_val not in src:
    print('Validation string not found')
    raise SystemExit(1)
src = src.replace(old_val, new_val)

old_block = '''
            // Jika admin memaksa ke dikembalikan tanpa melalui proses pengembalian,
            // kembalikan stok ke gudang (asumsi kondisi baik) agar stok tidak bocor.
            if ($statusBaru === 'dikembalikan') {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                    $detail->alat->syncStatusKondisi();
                }
            }
'''

if old_block not in src:
    print('Block not found')
    raise SystemExit(1)
src = src.replace(old_block, '')

p.write_text(src, encoding='utf-8')
print('Stage 1 Patched OK')
