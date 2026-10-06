<x-layouts.app title="Rekap Presensi">
    <div class="page-head">
        <div class="kicker">// laporan</div>
        <h1>Rekap Presensi</h1>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('admin.presensi.index') }}">
            <div class="grid2" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr))">
                <div class="field">
                    <label for="dari">Dari tanggal</label>
                    <input id="dari" type="date" name="dari" value="{{ $filter['dari'] }}">
                </div>
                <div class="field">
                    <label for="sampai">Sampai tanggal</label>
                    <input id="sampai" type="date" name="sampai" value="{{ $filter['sampai'] }}">
                </div>
                <div class="field">
                    <label for="mk">Mata kuliah</label>
                    <select id="mk" name="mata_kuliah_id">
                        <option value="">Semua</option>
                        @foreach ($matkuls as $mk)
                            <option value="{{ $mk->id }}" @selected((int) $filter['mata_kuliah_id'] === $mk->id)>{{ $mk->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="q">Cari NIM / nama</label>
                    <input id="q" type="search" name="q" value="{{ $filter['q'] }}">
                </div>
            </div>
            <div class="row">
                <button class="btn teal" type="submit"><i class="fas fa-filter"></i> Terapkan</button>
                <a class="btn" href="{{ route('admin.presensi.export', request()->query()) }}"><i class="fas fa-file-arrow-down"></i> Unduh CSV</a>
            </div>
            @if ($errors->any()) <div class="err-text" style="margin-top:8px">{{ $errors->first() }}</div> @endif
        </form>
    </div>

    <div class="card">
        <div class="card-head"><h2>{{ $presensis->total() }} catatan</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th>Jam</th><th>NIM</th><th>Nama</th><th>Kelas</th><th>Mata Kuliah</th><th>Sumber</th><th></th></tr></thead>
                <tbody>
                @forelse ($presensis as $p)
                    <tr>
                        <td class="mono">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        <td class="mono">{{ $p->waktu_hadir->format('H:i') }}</td>
                        <td class="mono">{{ $p->nim }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->kelas }}</td>
                        <td>{{ $p->mataKuliah?->nama }}</td>
                        <td><span class="badge {{ $p->sumber === 'admin' ? 'warn' : '' }}">{{ $p->sumber }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.presensi.destroy', $p) }}" data-confirm="Hapus catatan ini?">
                                @csrf @method('DELETE')
                                <button class="btn sm danger" type="submit"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Tidak ada data pada filter ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px">{{ $presensis->links() }}</div>
    </div>

<!-- Tabel Verifikasi Izin / Sakit oleh Admin -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Pengajuan Izin & Sakit (Pending)</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mata Kuliah</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bukti</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pengajuanIzin ?? [] as $izin)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $izin->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $izin->mataKuliah->nama ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $izin->kategori == 'sakit' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($izin->kategori) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <a href="{{ asset('storage/' . $izin->bukti_path) }}" target="_blank" class="text-indigo-600 hover:underline">
                                Lihat Bukti
                            </a>
                        </td>
                        <td class="px-4 py-2 text-sm flex space-x-2">
                            <form action="{{ route('admin.izin.update', $izin->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">
                                    Setujui
                                </button>
                            </form>

                            <form action="{{ route('admin.izin.update', $izin->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">
                                    Tolak
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada pengajuan izin yang menunggu persetujuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <div class="card" style="border-color:var(--brick)">
        <div class="card-head"><h2 style="color:var(--brick)">Zona Berbahaya</h2></div>
        <p class="muted" style="margin-top:0">Menghapus <b>seluruh</b> data presensi. Tindakan ini tidak dapat dibatalkan.</p>
        <form method="POST" action="{{ route('admin.presensi.reset') }}" class="row" data-confirm="Yakin menghapus SEMUA data presensi?">
            @csrf @method('DELETE')
            <input class="input" style="max-width:220px" name="konfirmasi" placeholder="Ketik HAPUS" required>
            <button class="btn danger" type="submit"><i class="fas fa-triangle-exclamation"></i> Hapus semua</button>
        </form>
        @error('konfirmasi') <div class="err-text">{{ $message }}</div> @enderror
    </div>
</x-layouts.app>
