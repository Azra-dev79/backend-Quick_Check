<x-layouts.guest title="Masuk">
    @php $role = old('role', 'mahasiswa'); @endphp
    <div class="auth">
        <section class="auth-poster">
            <div class="brand"><i class="fas fa-fingerprint"></i> QUICKCHECK</div>
            <h1>Absen <em>tanpa</em> repot, hasil presisi.</h1>
            <p>Presensi digital untuk kelas modern. Pindai QR, kehadiran langsung tercatat — tanpa kertas, tanpa antre.</p>
        </section>

        <section class="auth-card">
            <div class="card">
                <div class="kicker">// autentikasi</div>
                <h1 style="font-size:32px">Masuk.</h1>

                <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                    @csrf
                    <input type="hidden" name="role" id="roleInput" value="{{ $role }}">

                    <div class="tabs">
                        <button type="button" data-role="mahasiswa" @class(['on' => $role === 'mahasiswa'])><i class="fas fa-user-graduate"></i> Mahasiswa</button>
                        <button type="button" data-role="admin" @class(['on' => $role === 'admin'])><i class="fas fa-shield-halved"></i> Admin</button>
                    </div>

                    <div class="field">
                        <label for="username" id="userLabel">{{ $role === 'admin' ? 'Username' : 'NIM' }}</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>
                        @error('username') <div class="err">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" autocomplete="current-password" required>
                        @error('password') <div class="err">{{ $message }}</div> @enderror
                    </div>

                    <label class="check field"><input type="checkbox" name="remember" value="1" style="width:auto"> Ingat saya</label>

                    <button class="btn teal block" type="submit"><i class="fas fa-arrow-right"></i> Masuk sekarang</button>
                </form>

                <p class="muted" style="margin:16px 0 0;text-align:center">
                    Mahasiswa baru? <a href="{{ route('register') }}">Buat akun</a>
                </p>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.tabs button').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tabs button').forEach((b) => b.classList.remove('on'));
                btn.classList.add('on');
                document.getElementById('roleInput').value = btn.dataset.role;
                document.getElementById('userLabel').textContent = btn.dataset.role === 'admin' ? 'Username' : 'NIM';
            });
        });
    </script>
    @endpush
</x-layouts.guest>
