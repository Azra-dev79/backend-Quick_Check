<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MataKuliahRequest;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    public function index(): View
    {
        $matkuls = MataKuliah::withCount('users')->orderBy('nama')->get();

        return view('admin.matkul.index', compact('matkuls'));
    }

    public function store(MataKuliahRequest $request): RedirectResponse
    {
        MataKuliah::create($request->validated());

        return back()->with('status', 'Mata kuliah berhasil ditambahkan.');
    }

    public function update(MataKuliahRequest $request, MataKuliah $matkul): RedirectResponse
    {
        $matkul->update($request->validated());

        return back()->with('status', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $matkul): RedirectResponse
    {
        // Menghapus matkul juga menghapus riwayat presensinya (cascadeOnDelete di migrasi).
        $nama = $matkul->nama;
        $matkul->delete();

        return back()->with('status', "Mata kuliah {$nama} dihapus.");
    }
}
