<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\PresensiException;
use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\Izin;
use App\Services\PresensiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PresensiController extends Controller
{
    /** Rekap presensi dengan filter tanggal, mata kuliah, dan pencarian. */
    public function index(Request $request): View
    {
        [$query, $filter] = $this->queryDariFilter($request);

        $presensis = $query->paginate(20)->withQueryString();
        $matkuls = MataKuliah::orderBy('nama')->get();
        $pengajuanIzin = Izin::with(['user', 'mataKuliah'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        return view('admin.presensi.index', compact('presensis', 'matkuls', 'filter', 'pengajuanIzin'));
    }

    /** Unduh hasil filter yang sama sebagai berkas CSV (bisa dibuka di Excel). */
    public function export(Request $request): StreamedResponse
    {
        [$query, $filter] = $this->queryDariFilter($request);

        $namaFile = "presensi-{$filter['dari']}_sd_{$filter['sampai']}.csv";

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
            fputcsv($out, ['Tanggal', 'Jam', 'NIM', 'Nama', 'Kelas', 'Jurusan', 'Kode MK', 'Mata Kuliah', 'Status', 'Sumber']);

            // lazy() membaca data per potongan (chunk) sehingga hemat memori, dan tetap memuat relasi (with).
            foreach ($query->lazy() as $p) {
                fputcsv($out, [
                    $p->tanggal,
                    $p->waktu_hadir->format('H:i:s'),
                    $p->nim,
                    $p->nama,
                    $p->kelas,
                    $p->jurusan,
                    $p->mataKuliah?->kode,
                    $p->mataKuliah?->nama,
                    $p->status,
                    $p->sumber,
                ]);
            }

            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Admin mencatat presensi: dari QR pribadi mahasiswa (payload) atau NIM yang diketik. */
    public function store(Request $request, PresensiService $service): JsonResponse
    {
        $data = $request->validate([
            'mata_kuliah_id' => ['required', 'integer', 'exists:mata_kuliahs,id'],
            'payload' => ['nullable', 'string', 'max:2000', 'required_without:nim'],
            'nim' => ['nullable', 'string', 'max:20', 'required_without:payload'],
        ]);

        $mk = MataKuliah::findOrFail($data['mata_kuliah_id']);

        try {
            $presensi = $service->dariAdmin($request->user(), $mk, $data['payload'] ?? null, $data['nim'] ?? null);
        } catch (PresensiException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], $e->status);
        }

        return response()->json([
            'ok' => true,
            'message' => "{$presensi->nama} tercatat hadir di {$mk->nama}.",
            'data' => [
                'nim' => $presensi->nim,
                'nama' => $presensi->nama,
                'waktu' => $presensi->waktu_hadir->format('H:i'),
            ],
        ], 201);
    }

    public function destroy(Presensi $presensi): RedirectResponse
    {
        $presensi->delete();

        return back()->with('status', 'Catatan presensi dihapus.');
    }

    /** Hapus SELURUH presensi. Dilindungi dengan konfirmasi ketik "HAPUS". */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'konfirmasi' => ['required', 'in:HAPUS'],
        ]);

        Presensi::query()->delete();

        return back()->with('status', 'Seluruh data presensi telah dihapus.');
    }

    /**
     * Membangun query yang sama untuk halaman rekap dan ekspor CSV.
     *
     * @return array{0: Builder, 1: array<string, mixed>}
     */
    private function queryDariFilter(Request $request): array
    {
        $v = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'mata_kuliah_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $filter = [
            'dari' => $v['dari'] ?? today()->toDateString(),
            'sampai' => $v['sampai'] ?? ($v['dari'] ?? today()->toDateString()),
            'mata_kuliah_id' => $v['mata_kuliah_id'] ?? null,
            'q' => $v['q'] ?? '',
        ];

        $query = Presensi::query()
            ->with('mataKuliah')
            ->whereBetween('tanggal', [$filter['dari'], $filter['sampai']])
            ->when($filter['mata_kuliah_id'], fn ($q, $id) => $q->where('mata_kuliah_id', $id))
            ->when($filter['q'] !== '', fn ($q) => $q->where(function ($w) use ($filter) {
                $w->where('nim', 'like', "%{$filter['q']}%")
                    ->orWhere('nama', 'like', "%{$filter['q']}%");
            }))
            ->orderByDesc('waktu_hadir');

        return [$query, $filter];
    }
}
