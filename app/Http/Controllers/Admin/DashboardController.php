<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = today()->toDateString();

        return view('admin.dashboard', [
            'totalMahasiswa' => Mahasiswa::count(),
            'totalMatkul' => MataKuliah::count(),
            'totalAkun' => User::where('role', User::ROLE_MAHASISWA)->count(),
            'hadirHariIni' => Presensi::where('tanggal', $hariIni)->count(),
            // withCount + closure = hitung relasi dengan syarat tambahan, tanpa N+1 query.
            'perMatkul' => MataKuliah::withCount([
                'presensis as hadir_count' => fn ($q) => $q->where('tanggal', $hariIni),
            ])->orderBy('nama')->get(),
            'terbaru' => Presensi::with('mataKuliah')->latest('waktu_hadir')->limit(8)->get(),
        ]);
    }
}
