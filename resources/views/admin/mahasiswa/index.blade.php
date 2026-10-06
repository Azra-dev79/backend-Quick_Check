<x-layouts.app title="Data Mahasiswa">
    <div class="page-head">
        <div class="kicker">// master data</div>
        <h1>Data Mahasiswa</h1>
    </div>

    <div class="card">
        <div class="card-head">
            <form method="GET" class="row" style="flex:1">
                <input class="input" style="max-width:320px" type="search" name="q" value="{{ $q }}" placeholder="Cari NIM / nama / kelas…">
                <button class="btn sm" type="submit"><i class="fas fa-magnifying-glass"></i> Cari</button>
                @if ($q !== '') <a class="btn sm ghost" href="{{ route('admin.mahasiswa.index') }}">Reset</a> @endif
            </form>
            <div class="row">
                <button class="btn sm ghost" type="button" onclick="openModal('modalImport')"><i class="fas fa-file-csv"></i> Impor CSV</button>
                <button class="btn sm" type="button" id="btnTambah"><i class="fas fa-plus"></i> Tambah</button>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead><tr><th>NIM</th><th>Nama</th><th>Kelas</th><th>Jurusan</th><th>Hari ini</th><th></th></tr></thead>
                <tbody>
                @forelse ($mahasiswas as $m)
                    <tr>
                        <td class="mono">{{ $m->nim }}</td>
                        <td>{{ $m->nama }}</td>
                        <td>{{ $m->kelas }}</td>
                        <td>{{ $m->jurusan }}</td>
                        <td>
                            @if ($m->hadir_hari_ini) <span class="badge ok">Hadir</span> @else <span class="badge">—</span> @endif
                        </td>
                        <td>
                            <div class="actions">
                                <button type="button" class="btn sm btn-edit"
                                        data-mhs="{{ json_encode($m->only(['nim', 'nama', 'kelas', 'jurusan'])) }}"
                                        data-url="{{ route('admin.mahasiswa.update', $m) }}"><i class="fas fa-pen"></i></button>
                                <form method="POST" action="{{ route('admin.mahasiswa.destroy', $m) }}" data-confirm="Hapus data {{ $m->nama }}?">
                                    @csrf @method('DELETE')
                                    <button class="btn sm danger" type="submit"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada data mahasiswa.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:14px">{{ $mahasiswas->links() }}</div>
    </div>

    {{-- Modal tambah / ubah --}}
    <div class="modal" id="modalMhs">
        <div class="modal-box">
            <h2 id="modalMhsTitle">Tambah Mahasiswa</h2>
            <form method="POST" id="formMhs" action="{{ route('admin.mahasiswa.store') }}" novalidate>
                @csrf
                <span id="methodSlot"></span>
                <div class="field">
                    <label for="f_nim">NIM</label>
                    <input id="f_nim" name="nim" type="text" maxlength="20" value="{{ old('nim') }}" required>
                    @error('nim') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="f_nama">Nama</label>
                    <input id="f_nama" name="nama" type="text" value="{{ old('nama') }}" required>
                    @error('nama') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="grid2">
                    <div class="field">
                        <label for="f_kelas">Kelas</label>
                        <input id="f_kelas" name="kelas" type="text" maxlength="10" value="{{ old('kelas') }}" required>
                        @error('kelas') <div class="err">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label for="f_jurusan">Jurusan</label>
                        <input id="f_jurusan" name="jurusan" type="text" list="listJurusan" value="{{ old('jurusan') }}" required>
                        <datalist id="listJurusan">
                            @foreach (config('quickcheck.jurusan') as $j) <option value="{{ $j }}"> @endforeach
                        </datalist>
                        @error('jurusan') <div class="err">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row" style="justify-content:flex-end">
                    <button type="button" class="btn ghost" onclick="closeModal('modalMhs')">Batal</button>
                    <button type="submit" class="btn teal"><i class="fas fa-floppy-disk"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal impor CSV --}}
    <div class="modal" id="modalImport">
        <div class="modal-box">
            <h2>Impor dari CSV</h2>
            <p class="muted" style="font-size:14px">
                Baris pertama harus berisi judul kolom: <code>nim, nama, kelas, jurusan</code> (urutan bebas).
                Data dengan NIM yang sudah ada akan diperbarui. Dari Google Sheets: <i>File → Download → CSV</i>.
            </p>
            <form method="POST" action="{{ route('admin.mahasiswa.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label for="file">Berkas CSV</label>
                    <input id="file" type="file" name="file" accept=".csv,.txt" required>
                    @error('file') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="row" style="justify-content:flex-end">
                    <button type="button" class="btn ghost" onclick="closeModal('modalImport')">Batal</button>
                    <button type="submit" class="btn teal"><i class="fas fa-upload"></i> Impor</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const form = document.getElementById('formMhs');
        const storeUrl = @js(route('admin.mahasiswa.store'));

        function modeTambah() {
            form.action = storeUrl;
            document.getElementById('methodSlot').innerHTML = '';
            document.getElementById('modalMhsTitle').textContent = 'Tambah Mahasiswa';
            ['nim', 'nama', 'kelas', 'jurusan'].forEach((k) => document.getElementById('f_' + k).value = '');
            document.getElementById('f_nim').disabled = false;
            openModal('modalMhs');
        }

        document.getElementById('btnTambah').addEventListener('click', modeTambah);

        document.querySelectorAll('.btn-edit').forEach((b) => b.addEventListener('click', () => {
            const d = JSON.parse(b.dataset.mhs);
            form.action = b.dataset.url;
            // HTML form hanya mengenal GET/POST; Laravel membaca _method untuk PUT.
            document.getElementById('methodSlot').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('modalMhsTitle').textContent = 'Ubah Data Mahasiswa';
            ['nim', 'nama', 'kelas', 'jurusan'].forEach((k) => document.getElementById('f_' + k).value = d[k]);
            document.getElementById('f_nim').disabled = true; // NIM tidak boleh diganti
            openModal('modalMhs');
        }));

        @if ($errors->has('nim') || $errors->has('nama') || $errors->has('kelas') || $errors->has('jurusan'))
            openModal('modalMhs');
        @endif
        @if ($errors->has('file'))
            openModal('modalImport');
        @endif
    </script>
    @endpush
</x-layouts.app>
