<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Presensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $diambil = $user->mataKuliahs()->orderBy('nama')->get();
        $tersedia = MataKuliah::whereNotIn('id', $diambil->pluck('id'))->orderBy('nama')->get();
        $totalHadir = Presensi::where('nim', $user->nim)->count();

        return view('mahasiswa.profil', compact('user', 'diambil', 'tersedia', 'totalHadir'));
    }

    public function updateBiodata(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'kelas' => ['required', 'string', 'max:10'],
            'jurusan' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email,'.$request->user()->id],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Biodata berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:'.config('quickcheck.min_password'), 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Password berhasil diganti.');
    }

    public function ambilMatkul(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mata_kuliah_id' => ['required', 'integer', 'exists:mata_kuliahs,id'],
        ]);

        // syncWithoutDetaching = tambahkan bila belum ada, tanpa menghapus yang lain.
        $request->user()->mataKuliahs()->syncWithoutDetaching([$data['mata_kuliah_id']]);

        return back()->with('status', 'Mata kuliah berhasil ditambahkan.');
    }

    public function lepasMatkul(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $request->user()->mataKuliahs()->detach($mataKuliah->id);

        return back()->with('status', "Mata kuliah {$mataKuliah->nama} dilepas.");
    }
}
