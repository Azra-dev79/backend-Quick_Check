<x-layouts.app title="Presensi">
    <div class="page-head">
        <div class="kicker">// presensi mahasiswa</div>
        <h1>Halo, {{ $user->name }}</h1>
        <p class="muted">{{ $user->nim }} · {{ $user->kelas }} · {{ $user->jurusan }}</p>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>Pindai QR Kelas</h2>
            <span class="badge" id="statusBadge">Kamera mati</span>
        </div>

        <div id="reader" class="scanner"><span>Tekan “Mulai Scan”, lalu arahkan kamera ke QR yang ditampilkan dosen.</span></div>
        <div class="hint" id="hint">Siap untuk scan</div>

        <div class="row" style="justify-content:center;margin-top:14px">
            <button class="btn teal" id="btnScan" type="button"><i class="fas fa-play"></i> <span>Mulai Scan</span></button>
        </div>
        <p class="muted" style="text-align:center;font-size:13px;margin-bottom:0">
        </p>
    </div>
    
<div class="card" style="margin-top: 24px;">
    <div class="card-head">
        <h2>Form Pengajuan Izin / Sakit</h2>
    </div>

    @if(session('success'))
        <div style="padding: 12px; background-color: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 16px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #374151;">Mata Kuliah</label>
            <select name="mata_kuliah_id" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background-color: #fff;" required>
            <option value="">-- Pilih Mata Kuliah --</option>
            @foreach($matkuls ?? [] as $matkul)
            <option value="{{ $matkul->id }}">{{ $matkul->nama }}</option>
            @endforeach
        </select>
    </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #374151;">Tanggal</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background-color: #fff;" required>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #374151;">Kategori Ketidakhadiran</label>
            <div style="display: flex; gap: 20px; align-items: center; font-size: 14px; color: #374151;">
                <label style="cursor: pointer;"><input type="radio" name="kategori" value="sakit" checked style="margin-right: 6px;"> Sakit</label>
                <label style="cursor: pointer;"><input type="radio" name="kategori" value="izin" style="margin-right: 6px;"> Izin</label>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #374151;">Alasan</label>
            <textarea name="alasan" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background-color: #fff;" placeholder="Tuliskan alasan tidak hadir..." required></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #374151;">Upload Bukti (Foto Surat / Keterangan)</label>
            <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required style="width: 100%; font-size: 14px; color: #4b5563;">
        </div>

        <button type="submit" class="btn teal" style="width: 100%; justify-content: center; padding: 10px; font-size: 14px; font-weight: 600;">
            Kirim Pengajuan
        </button>
        </form>
    </div>

    <div class="card">
        <div class="card-head"><h2>Mata Kuliah Saya · Hari Ini</h2>
            <a class="btn sm ghost" href="{{ route('profil') }}"><i class="fas fa-plus"></i> Kelola matkul</a>
        </div>

        @forelse ($matkuls as $mk)
            @php $hadir = $hadirHariIni->get($mk->id); @endphp
            <div class="list-item">
                <div>
                    <b>{{ $mk->nama }}</b><br>
                    <span class="muted mono" style="font-size:12px">{{ $mk->kode }} · {{ $mk->sks }} SKS</span>
                </div>
                @if ($hadir)
                    <span class="badge ok"><i class="fas fa-check"></i> Hadir {{ $hadir->waktu_hadir->format('H:i') }}</span>
                @else
                    <span class="badge warn">Belum absen</span>
                @endif
            </div>
        @empty
            <div class="empty">Kamu belum mengambil mata kuliah. Buka halaman <a href="{{ route('profil') }}">Profil</a> untuk menambahkannya.</div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-head"><h2>Riwayat Terakhir</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th>Jam</th><th>Mata Kuliah</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($riwayat as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</td>
                        <td class="mono">{{ $p->waktu_hadir->format('H:i') }}</td>
                        <td>{{ $p->mataKuliah?->nama }}</td>
                        <td><span class="badge ok">{{ $p->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada riwayat presensi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        const hint = document.getElementById('hint');
        const badge = document.getElementById('statusBadge');
        const btn = document.getElementById('btnScan');
        let busy = false;

        function setHint(text, cls) {
            hint.textContent = text;
            hint.className = 'hint' + (cls ? ' ' + cls : '');
        }
        function setButton(running) {
            btn.querySelector('span').textContent = running ? 'Hentikan' : 'Mulai Scan';
            btn.querySelector('i').className = running ? 'fas fa-stop' : 'fas fa-play';
            badge.textContent = running ? 'Kamera aktif' : 'Kamera mati';
        }

        const scanner = createScanner('reader', async (text) => {
            if (busy) return;
            busy = true;
            await scanner.stop();
            setButton(false);
            setHint('Memproses…');

            const { ok, json } = await postJson(@js(route('absensi.scan')), { payload: text });

            if (ok) {
                setHint('✓ ' + json.message, 'ok');
                toast('success', 'Presensi berhasil', json.message);
                setTimeout(() => location.reload(), 1600);
            } else {
                setHint(json.message, 'bad');
                toast('error', 'Presensi gagal', json.message);
                setTimeout(() => { busy = false; setHint('Siap untuk scan'); }, 2500);
            }
        });

        btn.addEventListener('click', async () => {
            if (scanner.running) { await scanner.stop(); setButton(false); return; }
            await scanner.start();
            setButton(scanner.running);
        });
    </script>
    @endpush
</x-layouts.app>
