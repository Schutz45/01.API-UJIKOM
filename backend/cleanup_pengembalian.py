import pathlib, re

p = pathlib.Path('app/Http/Controllers/AdminController.php')
src = p.read_text(encoding='utf-8')

# 1. Remove Edit, Update, Destroy
# Pattern: find the method start and find its balanced closing brace is hard with regex,
# so we match the specific known method signatures and their blocks.

methods_to_remove = [
    r'public function editPengembalian.*?view\(\'admin\.pengembalian\.edit\', compact\(\'pengembalian\'\)\);\s*\}',
    r'public function updatePengembalian.*?DB::rollBack\(\);\s*return back\(\)\s*->withInput\(\)\s*->with\(\s*\'error\',\s*\'Gagal memperbarui pengembalian: \' \. $e->getMessage\(\)\s*\);\s*\}\s*\}',
    r'public function destroyPengembalian.*?DB::rollBack\(\);\s*return back\(\)\s*->with\(\s*\'error\',\s*\'Gagal menghapus pengembalian: \' \. $e->getMessage\(\)\s*\);\s*\}\s*\}'
]

for pattern in methods_to_remove:
    src = re.sub(pattern, '', src, flags=re.DOTALL)

# 2. Add showPengembalian before the last class brace
show_method = '''
    public function showPengembalian()
    {
         = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ])->findOrFail();

        return view('admin.pengembalian.show', compact('pengembalian'));
    }
'''

# Find the last brace and insert before it
src = src.rstrip()
if src.endswith('}'):
    # Remove last brace
    src = src[:-1].rstrip()
    if src.endswith('}'): # Case with nested braces
         src = src[:-1].rstrip()
         src += show_method + n}n}
    else:
         src += show_method + n}n

p.write_text(src, encoding='utf-8')
print('Methods cleaned and showPengembalian added')
