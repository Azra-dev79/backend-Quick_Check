<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $mahasiswas = Mahasiswa::query()
            // withExists menambah kolom boolean `hadir_hari_ini` tanpa query tambahan per baris.
            ->withExists(['presensis as hadir_hari_ini' => fn ($p) => $p->where('tanggal', today()->toDateString())])
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('nim', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('kelas', 'like', "%{$q}%");
            }))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString(); // agar kata kunci pencarian terbawa di link halaman berikutnya

        return view('admin.mahasiswa.index', compact('mahasiswas', 'q'));
    }

    public function store(MahasiswaRequest $request): RedirectResponse
    {
        Mahasiswa::create($request->validated());

        return back()->with('status', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function update(MahasiswaRequest $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->update($request->validated());

        return back()->with('status', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $nama = $mahasiswa->nama;
        $mahasiswa->delete();

        return back()->with('status', "Data {$nama} dihapus.");
    }

    /**
     * Impor massal dari CSV (mis. hasil "Download > CSV" dari Google Sheets).
     * Kolom yang dibaca: nim, nama, kelas, jurusan (urutan bebas, huruf besar/kecil bebas).
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['file' => 'Berkas tidak dapat dibaca.']);
        }

        // Deteksi pemisah kolom: Excel versi Indonesia biasanya memakai titik-koma.
        $barisPertama = (string) fgets($handle);
        $barisPertama = preg_replace('/^\xEF\xBB\xBF/', '', $barisPertama); // buang BOM
        $pemisah = substr_count($barisPertama, ';') > substr_count($barisPertama, ',') ? ';' : ',';

        $header = array_map(fn ($h) => strtolower(trim($h)), str_getcsv($barisPertama, $pemisah));
        $kolom = array_flip($header);

        foreach (['nim', 'nama'] as $wajib) {
            if (! isset($kolom[$wajib])) {
                fclose($handle);

                return back()->withErrors(['file' => "Kolom \"{$wajib}\" tidak ditemukan pada baris judul CSV."]);
            }
        }

        $baru = 0;
        $diperbarui = 0;
        $dilewati = 0;

        while (($baris = fgetcsv($handle, 0, $pemisah)) !== false) {
            $nim = trim((string) ($baris[$kolom['nim']] ?? ''));
            $nama = trim((string) ($baris[$kolom['nama']] ?? ''));

            if ($nim === '' || $nama === '') {
                $dilewati++;
                continue;
            }

            $mhs = Mahasiswa::updateOrCreate(
                ['nim' => $nim],
                [
                    'nama' => $nama,
                    'kelas' => trim((string) ($baris[$kolom['kelas'] ?? -1] ?? '')) ?: '-',
                    'jurusan' => trim((string) ($baris[$kolom['jurusan'] ?? -1] ?? '')) ?: '-',
                ]
            );

            $mhs->wasRecentlyCreated ? $baru++ : $diperbarui++;
        }

        fclose($handle);

        return back()->with('status', "Impor selesai: {$baru} baru, {$diperbarui} diperbarui, {$dilewati} dilewati.");
    }
}
