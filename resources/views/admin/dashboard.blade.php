<x-layouts.app title="Dashboard Admin">
    <div class="page-head">
        <div class="kicker">// panel administrator · {{ now()->translatedFormat('l, d F Y') }}</div>
        <h1>Dashboard</h1>
    </div>

    <div class="stats">
        <div class="stat"><div class="num">{{ $hadirHariIni }}</div><div class="lbl">Hadir hari ini</div></div>
        <div class="stat"><div class="num">{{ $totalMahasiswa }}</div><div class="lbl">Data mahasiswa</div></div>
        <div class="stat"><div class="num">{{ $totalAkun }}</div><div class="lbl">Akun terdaftar</div></div>
        <div class="stat"><div class="num">{{ $totalMatkul }}</div><div class="lbl">Mata kuliah</div></div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Kehadiran Hari Ini per Mata Kuliah</h2>
            <a class="btn sm" href="{{ route('admin.qr.index') }}"><i class="fas fa-qrcode"></i> Tampilkan QR</a>
        </div>
        @forelse ($perMatkul as $mk)
            <div class="list-item">
                <div><b>{{ $mk->nama }}</b> <span class="muted mono" style="font-size:12px">{{ $mk->kode }}</span></div>
                <span class="badge {{ $mk->hadir_count > 0 ? 'ok' : '' }}">{{ $mk->hadir_count }} hadir</span>
            </div>
        @empty
            <div class="empty">Belum ada mata kuliah. <a href="{{ route('admin.matkul.index') }}">Tambahkan sekarang</a>.</div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-head"><h2>Presensi Terbaru</h2>
            <a class="btn sm ghost" href="{{ route('admin.presensi.index') }}">Lihat semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Waktu</th><th>NIM</th><th>Nama</th><th>Mata Kuliah</th></tr></thead>
                <tbody>
                @forelse ($terbaru as $p)
                    <tr>
                        <td class="mono">{{ $p->waktu_hadir->format('d/m H:i') }}</td>
                        <td class="mono">{{ $p->nim }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->mataKuliah?->nama }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada presensi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
