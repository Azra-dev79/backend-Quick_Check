<x-layouts.guest title="Daftar">
    <div class="auth">
        <section class="auth-poster">
            <div class="brand"><i class="fas fa-fingerprint"></i> QUICKCHECK</div>
            <h1>Buat <em>akun</em> mahasiswa.</h1>
            <p>Isi data dirimu dengan benar. Akun langsung aktif dan datamu otomatis tersimpan, tanpa menunggu admin.</p>
        </section>

        <section class="auth-card">
            <div class="card">
                <div class="kicker">// registrasi</div>
                <h1 style="font-size:32px">Daftar.</h1>

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label for="nama">Nama lengkap</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Sesuai data kampus" required autofocus>
                        @error('nama') <div class="err">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="nim">NIM</label>
                        <input type="text" id="nim" name="nim" value="{{ old('nim') }}" maxlength="20" required>
                        @error('nim') <div class="err">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid2">
                        <div class="field">
                            <label for="kelas">Kelas</label>
                            <input type="text" id="kelas" name="kelas" value="{{ old('kelas') }}" maxlength="10" placeholder="mis. 2A" required>
                            @error('kelas') <div class="err">{{ $message }}</div> @enderror
                        </div>
                        <div class="field">
                            <label for="jurusan">Jurusan</label>
                            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan') }}" list="listJurusan" required>
                            <datalist id="listJurusan">
                                @foreach (config('quickcheck.jurusan') as $j) <option value="{{ $j }}"> @endforeach
                            </datalist>
                            @error('jurusan') <div class="err">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Password (min. {{ config('quickcheck.min_password') }} karakter)</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required>
                        @error('password') <div class="err">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Ulangi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                    </div>

                    <button class="btn teal block" type="submit"><i class="fas fa-user-plus"></i> Buat akun</button>
                </form>

                <p class="muted" style="margin:16px 0 0;text-align:center">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                </p>
            </div>
        </section>
    </div>
</x-layouts.guest>
