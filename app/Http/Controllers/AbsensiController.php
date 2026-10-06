<?php

namespace App\Http\Controllers;
use App\Exceptions\PresensiException;
use App\Models\Presensi;
use App\Services\PresensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman presensi milik mahasiswa (scan QR kelas + riwayat). */
class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
    
    // Ambil data mata kuliah milik mahasiswa
    $matkuls = $user ? $user->mataKuliahs : collect();

    // Ambil data kehadiran hari ini
    $hadirHariIni = \App\Models\Presensi::where('nim', $user->nim ?? '')
        ->where('tanggal', \Carbon\Carbon::today())
        ->get()
        ->keyBy('mata_kuliah_id');

    // --- TAMBAHKAN KODE INI UNTUK MENGAMBIL RIWAYAT ---
    $riwayat = \App\Models\Presensi::where('nim', $user->nim ?? '')
        ->with('mataKuliah')
        ->latest()
        ->take(10)
        ->get();

    // Kirim semua variabel ke view termasuk $riwayat
    return view('mahasiswa.absensi', compact('user', 'matkuls', 'hadirHariIni', 'riwayat'));

        $user = $request->user();
        $hariIni = today()->toDateString();

        $matkuls = $user->mataKuliahs()->orderBy('nama')->get();

        // Presensi hari ini, diindeks per id matkul agar mudah dicek di view.
        $hadirHariIni = Presensi::where('nim', $user->nim)
            ->where('tanggal', $hariIni)
            ->get()
            ->keyBy('mata_kuliah_id');

        $riwayat = Presensi::with('mataKuliah')
            ->where('nim', $user->nim)
            ->latest('waktu_hadir')
            ->limit(20)
            ->get();

        return view('mahasiswa.absensi', compact('user', 'matkuls', 'hadirHariIni', 'riwayat'));
    }

    /** Dipanggil lewat fetch() oleh JavaScript setelah kamera membaca QR. */
    public function scan(Request $request, PresensiService $service): JsonResponse
    {
        $data = $request->validate([
            'payload' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $presensi = $service->dariQrKelas($request->user(), $data['payload']);
        } catch (PresensiException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], $e->status);
        }

        return response()->json([
            'ok' => true,
            'message' => "Presensi {$presensi->mataKuliah->nama} berhasil dicatat.",
            'data' => [
                'matkul' => $presensi->mataKuliah->nama,
                'waktu' => $presensi->waktu_hadir->format('H:i'),
            ],
        ], 201);
    }
}
