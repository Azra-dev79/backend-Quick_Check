<x-layouts.app title="Scan Admin">
    <div class="page-head">
        <div class="kicker">// pencatatan manual</div>
        <h1>Scan Presensi</h1>
        <p class="muted">Catat kehadiran mahasiswa dengan memindai QR pribadinya atau mengetik NIM.</p>
    </div>

    @if ($matkuls->isEmpty())
        <div class="card"><div class="empty">Belum ada mata kuliah. <a href="{{ route('admin.matkul.index') }}">Tambahkan dulu</a>.</div></div>
    @else
    <div class="card">
        <div class="field" style="max-width:420px">
            <label for="selMk">Mata kuliah</label>
            <select id="selMk">
                @foreach ($matkuls as $mk)
                    <option value="{{ $mk->id }}">{{ $mk->label }}</option>
                @endforeach
            </select>
        </div>

        <div id="reader" class="scanner"><span>Tekan “Mulai Scan” untuk membuka kamera.</span></div>
        <div class="hint" id="hint">Siap untuk scan</div>
        <div class="row" style="justify-content:center;margin-top:14px">
            <button class="btn teal" id="btnScan" type="button"><i class="fas fa-play"></i> <span>Mulai Scan</span></button>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Input NIM Manual</h2></div>
        <div class="row">
            <input class="input" style="max-width:260px" id="nimManual" placeholder="NIM mahasiswa" inputmode="numeric">
            <button class="btn" type="button" id="btnManual"><i class="fas fa-check"></i> Catat hadir</button>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Tercatat pada sesi ini</h2></div>
        <div id="log"><div class="empty">Belum ada.</div></div>
    </div>
    @endif

    @if ($matkuls->isNotEmpty())
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        const url = @js(route('admin.presensi.store'));
        const sel = document.getElementById('selMk');
        const hint = document.getElementById('hint');
        const btn = document.getElementById('btnScan');
        const log = document.getElementById('log');
        let busy = false;

        function setHint(t, c) { hint.textContent = t; hint.className = 'hint' + (c ? ' ' + c : ''); }
        function tambahLog(ok, text) {
            if (log.querySelector('.empty')) log.innerHTML = '';
            const row = document.createElement('div');
            row.className = 'list-item';
            const span = document.createElement('span');
            span.textContent = text;
            const badge = document.createElement('span');
            badge.className = 'badge ' + (ok ? 'ok' : 'bad');
            badge.textContent = ok ? 'OK' : 'Gagal';
            row.append(span, badge);
            log.prepend(row);
        }

        async function kirim(body) {
            const { ok, json } = await postJson(url, { mata_kuliah_id: sel.value, ...body });
            setHint(json.message, ok ? 'ok' : 'bad');
            toast(ok ? 'success' : 'error', ok ? 'Tercatat' : 'Gagal', json.message);
            tambahLog(ok, json.message);
            return ok;
        }

        const scanner = createScanner('reader', async (text) => {
            if (busy) return;
            busy = true;
            await kirim({ payload: text });
            setTimeout(() => { busy = false; setHint('Siap untuk scan'); }, 2200); // kamera tetap aktif untuk mahasiswa berikutnya
        });

        btn.addEventListener('click', async () => {
            if (scanner.running) { await scanner.stop(); }
            else { await scanner.start(); }
            btn.querySelector('span').textContent = scanner.running ? 'Hentikan' : 'Mulai Scan';
            btn.querySelector('i').className = scanner.running ? 'fas fa-stop' : 'fas fa-play';
        });

        document.getElementById('btnManual').addEventListener('click', async () => {
            const nim = document.getElementById('nimManual').value.trim();
            if (!nim) { toast('warning', 'NIM kosong', 'Ketik NIM terlebih dahulu.'); return; }
            if (await kirim({ nim })) document.getElementById('nimManual').value = '';
        });
    </script>
    @endpush
    @endif
</x-layouts.app>
