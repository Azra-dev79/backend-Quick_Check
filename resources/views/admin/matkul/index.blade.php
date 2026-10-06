<x-layouts.app title="Mata Kuliah">
    <div class="page-head">
        <div class="kicker">// master mata kuliah</div>
        <h1>Mata Kuliah</h1>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>{{ $matkuls->count() }} mata kuliah</h2>
            <button class="btn sm" type="button" id="btnTambah"><i class="fas fa-plus"></i> Tambah</button>
        </div>

        <div class="cards">
            @forelse ($matkuls as $mk)
                <div class="mini">
                    <span class="code">{{ $mk->kode }}</span>
                    <h3 style="font-size:17px">{{ $mk->nama }}</h3>
                    <div style="font-size:13px;opacity:.85">
                        {{ $mk->sks }} SKS{{ $mk->dosen ? ' · '.$mk->dosen : '' }}<br>
                        <i class="fas fa-users"></i> {{ $mk->users_count }} peserta
                    </div>
                    @if ($mk->deskripsi) <p style="font-size:13px;margin:8px 0 0;opacity:.85">{{ $mk->deskripsi }}</p> @endif
                    <div class="actions" style="margin-top:12px">
                        <button type="button" class="btn sm btn-edit"
                                data-mk="{{ json_encode($mk->only(['kode', 'nama', 'sks', 'dosen', 'deskripsi'])) }}"
                                data-url="{{ route('admin.matkul.update', $mk) }}"><i class="fas fa-pen"></i> Ubah</button>
                        <form method="POST" action="{{ route('admin.matkul.destroy', $mk) }}"
                              data-confirm="Hapus {{ $mk->nama }}? Riwayat presensinya ikut terhapus.">
                            @csrf @method('DELETE')
                            <button class="btn sm danger" type="submit"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty" style="grid-column:1/-1">Belum ada mata kuliah.</div>
            @endforelse
        </div>
    </div>

    <div class="modal" id="modalMk">
        <div class="modal-box">
            <h2 id="modalMkTitle">Tambah Mata Kuliah</h2>
            <form method="POST" id="formMk" action="{{ route('admin.matkul.store') }}" novalidate>
                @csrf
                <span id="methodSlot"></span>
                <div class="grid2">
                    <div class="field">
                        <label for="f_kode">Kode</label>
                        <input id="f_kode" name="kode" type="text" maxlength="20" value="{{ old('kode') }}" required>
                        @error('kode') <div class="err">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label for="f_sks">SKS</label>
                        <input id="f_sks" name="sks" type="number" min="1" max="6" value="{{ old('sks', 3) }}" required>
                        @error('sks') <div class="err">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="field">
                    <label for="f_nama">Nama mata kuliah</label>
                    <input id="f_nama" name="nama" type="text" value="{{ old('nama') }}" required>
                    @error('nama') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="f_dosen">Dosen (opsional)</label>
                    <input id="f_dosen" name="dosen" type="text" value="{{ old('dosen') }}">
                    @error('dosen') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="f_deskripsi">Deskripsi (opsional)</label>
                    <textarea id="f_deskripsi" name="deskripsi" rows="2" maxlength="500">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="row" style="justify-content:flex-end">
                    <button type="button" class="btn ghost" onclick="closeModal('modalMk')">Batal</button>
                    <button type="submit" class="btn teal"><i class="fas fa-floppy-disk"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const form = document.getElementById('formMk');
        const storeUrl = @js(route('admin.matkul.store'));
        const fields = ['kode', 'nama', 'sks', 'dosen', 'deskripsi'];

        document.getElementById('btnTambah').addEventListener('click', () => {
            form.action = storeUrl;
            document.getElementById('methodSlot').innerHTML = '';
            document.getElementById('modalMkTitle').textContent = 'Tambah Mata Kuliah';
            fields.forEach((k) => document.getElementById('f_' + k).value = k === 'sks' ? 3 : '');
            openModal('modalMk');
        });

        document.querySelectorAll('.btn-edit').forEach((b) => b.addEventListener('click', () => {
            const d = JSON.parse(b.dataset.mk);
            form.action = b.dataset.url;
            document.getElementById('methodSlot').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('modalMkTitle').textContent = 'Ubah Mata Kuliah';
            fields.forEach((k) => document.getElementById('f_' + k).value = d[k] ?? '');
            openModal('modalMk');
        }));

        @if ($errors->any())
            openModal('modalMk');
        @endif
    </script>
    @endpush
</x-layouts.app>
