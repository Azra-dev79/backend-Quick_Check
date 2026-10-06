<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Support\QrToken;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QrController extends Controller
{
    /** Halaman pembuat QR: QR kelas per mata kuliah (berlaku hari ini) dan QR pribadi mahasiswa. */
    public function index(): View
    {
        $matkuls = MataKuliah::orderBy('nama')->get()->map(fn (MataKuliah $mk) => [
            'id' => $mk->id,
            'label' => $mk->label,
            'nama' => $mk->nama,
            'payload' => QrToken::kelas($mk),
        ]);

        return view('admin.qr', compact('matkuls'));
    }

    /** JSON berisi isi QR pribadi. Route memakai {mahasiswa:nim} sehingga pencarian lewat kolom nim. */
    public function mahasiswa(Mahasiswa $mahasiswa): JsonResponse
    {
        return response()->json([
            'nim' => $mahasiswa->nim,
            'nama' => $mahasiswa->nama,
            'kelas' => $mahasiswa->kelas,
            'jurusan' => $mahasiswa->jurusan,
            'payload' => QrToken::mahasiswa($mahasiswa),
        ]);
    }

    /** Halaman pemindai untuk admin (scan QR pribadi mahasiswa / input NIM manual). */
    public function scan(): View
    {
        $matkuls = MataKuliah::orderBy('nama')->get();

        return view('admin.scan', compact('matkuls'));
    }
}
