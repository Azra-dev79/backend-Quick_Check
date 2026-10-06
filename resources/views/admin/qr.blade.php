<x-layouts.app title="QR Code">
    <div class="page-head">
        <div class="kicker">// generator</div>
        <h1>QR Code</h1>
    </div>

    <div class="card">
        <div class="card-head"><h2>QR Presensi Kelas</h2><span class="badge warn">Berlaku hari ini: {{ today()->translatedFormat('d M Y') }}</span></div>
        <p class="muted" style="margin-top:0">Tampilkan QR ini di layar/proyektor. Mahasiswa memindainya dari halaman Presensi. QR otomatis kedaluwarsa besok.</p>

        @if ($matkuls->isEmpty())
            <div class="empty">Belum ada mata kuliah. <a href="{{ route('admin.matkul.index') }}">Tambahkan dulu</a>.</div>
        @else
            <div class="field" style="max-width:420px">
                <label for="selMk">Mata kuliah</label>
                <select id="selMk">
                    @foreach ($matkuls as $mk)
                        <option value="{{ $mk['id'] }}">{{ $mk['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="qr-box">
                <div class="qr-canvas" id="qrKelas"></div>
                <div class="mono" id="qrKelasName"></div>
                <button class="btn sm" type="button" id="btnDlKelas"><i class="fas fa-download"></i> Unduh PNG</button>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-head"><h2>QR Pribadi Mahasiswa</h2></div>
        <p class="muted" style="margin-top:0">Untuk mahasiswa yang kameranya bermasalah: admin memindainya lewat menu <a href="{{ route('admin.scan') }}">Scan</a>.</p>
        <div class="row" style="margin-bottom:14px">
            <input class="input" style="max-width:260px" id="nimInput" placeholder="Masukkan NIM" inputmode="numeric">
            <button class="btn teal" type="button" id="btnBuat"><i class="fas fa-qrcode"></i> Buat QR</button>
        </div>
        <div class="qr-box" id="boxPribadi" style="display:none">
            <div class="qr-canvas" id="qrPribadi"></div>
            <div id="infoPribadi" style="text-align:center"></div>
            <button class="btn sm" type="button" id="btnDlPribadi"><i class="fas fa-download"></i> Unduh PNG</button>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const matkuls = @js($matkuls);
        const kelasBox = document.getElementById('qrKelas');
        const pribadiBox = document.getElementById('qrPribadi');
        const sel = document.getElementById('selMk');

        function tampilKelas() {
            if (!sel) return;
            const mk = matkuls.find((m) => String(m.id) === sel.value);
            renderQr(kelasBox, mk.payload, 300);
            document.getElementById('qrKelasName').textContent = mk.label;
        }
        if (sel) {
            sel.addEventListener('change', tampilKelas);
            tampilKelas();
            document.getElementById('btnDlKelas').addEventListener('click', () => downloadQr(kelasBox, 'qr-kelas-' + sel.value + '.png'));
        }

        document.getElementById('btnBuat').addEventListener('click', async () => {
            const nim = document.getElementById('nimInput').value.trim();
            if (!nim) { toast('warning', 'NIM kosong', 'Masukkan NIM terlebih dahulu.'); return; }

            const res = await fetch(@js(url('/admin/qr/mahasiswa')) + '/' + encodeURIComponent(nim), {
                headers: { 'Accept': 'application/json' }, credentials: 'same-origin',
            });
            if (!res.ok) { toast('error', 'Tidak ditemukan', 'NIM ' + nim + ' tidak terdaftar.'); return; }

            const d = await res.json();
            renderQr(pribadiBox, d.payload, 260);
            const info = document.getElementById('infoPribadi');
            info.textContent = '';
            const b = document.createElement('b'); b.textContent = d.nama;
            info.append(b, document.createElement('br'), d.nim + ' · ' + d.kelas + ' · ' + d.jurusan);
            document.getElementById('boxPribadi').style.display = 'flex';
            document.getElementById('btnDlPribadi').onclick = () => downloadQr(pribadiBox, 'qr-' + d.nim + '.png');
        });
    </script>
    @endpush
</x-layouts.app>
