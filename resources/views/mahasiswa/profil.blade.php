<x-layouts.app title="Profil Saya">
    <div class="page-head">
        <div class="kicker">// akun</div>
        <h1>Profil Saya</h1>
    </div>

    <div class="stats">
        <div class="stat"><div class="num">{{ $totalHadir }}</div><div class="lbl">Total kehadiran</div></div>
        <div class="stat"><div class="num">{{ $diambil->count() }}</div><div class="lbl">Mata kuliah diambil</div></div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Biodata</h2></div>
        <form method="POST" action="{{ route('profil.update') }}" novalidate>
            @csrf @method('PUT')
            <div class="grid2">
                <div class="field">
                    <label for="nim">NIM</label>
                    <input id="nim" type="text" value="{{ $user->nim }}" disabled>
                </div>
                <div class="field">
                    <label for="name">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="kelas">Kelas</label>
                    <input id="kelas" name="kelas" type="text" maxlength="10" value="{{ old('kelas', $user->kelas) }}" required>
                    @error('kelas') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="jurusan">Jurusan</label>
                    <input id="jurusan" name="jurusan" type="text" list="listJurusan" value="{{ old('jurusan', $user->jurusan) }}" required>
                    <datalist id="listJurusan">
                        @foreach (config('quickcheck.jurusan') as $j)
                            <option value="{{ $j }}">
                        @endforeach
                    </datalist>
                    @error('jurusan') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="email">Email (opsional)</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}">
                    @error('email') <div class="err">{{ $message }}</div> @enderror
                </div>
            </div>
            <button class="btn teal" type="submit"><i class="fas fa-floppy-disk"></i> Simpan biodata</button>
        </form>
    </div>

    <div class="card">
        <div class="card-head"><h2>Mata Kuliah</h2></div>

        <form method="POST" action="{{ route('profil.matkul.ambil') }}" class="row" style="margin-bottom:16px">
            @csrf
            <select name="mata_kuliah_id" class="input" style="flex:1;min-width:220px" required>
                <option value="">— Pilih mata kuliah —</option>
                @foreach ($tersedia as $mk)
                    <option value="{{ $mk->id }}">{{ $mk->label }} ({{ $mk->sks }} SKS)</option>
                @endforeach
            </select>
            <button class="btn" type="submit" @disabled($tersedia->isEmpty())><i class="fas fa-plus"></i> Ambil</button>
        </form>

        @forelse ($diambil as $mk)
            <div class="list-item">
                <div>
                    <b>{{ $mk->nama }}</b><br>
                    <span class="muted mono" style="font-size:12px">{{ $mk->kode }} · {{ $mk->sks }} SKS{{ $mk->dosen ? ' · '.$mk->dosen : '' }}</span>
                </div>
                <form method="POST" action="{{ route('profil.matkul.lepas', $mk) }}" data-confirm="Lepas mata kuliah {{ $mk->nama }}?">
                    @csrf @method('DELETE')
                    <button class="btn sm danger" type="submit"><i class="fas fa-xmark"></i> Lepas</button>
                </form>
            </div>
        @empty
            <div class="empty">Belum ada mata kuliah yang diambil.</div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-head"><h2>Ganti Password</h2></div>
        <form method="POST" action="{{ route('profil.password') }}" novalidate>
            @csrf @method('PUT')
            <div class="grid2">
                <div class="field">
                    <label for="current_password">Password saat ini</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                    @error('current_password') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div></div>
                <div class="field">
                    <label for="password">Password baru</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required>
                    @error('password') <div class="err">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="password_confirmation">Ulangi password baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                </div>
            </div>
            <button class="btn teal" type="submit"><i class="fas fa-key"></i> Ganti password</button>
        </form>
    </div>
</x-layouts.app>
