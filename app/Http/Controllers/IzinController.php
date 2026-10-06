<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\Presensi;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'tanggal'        => 'required|date',
            'kategori'       => 'required|in:sakit,izin',
            'alasan'         => 'required|string|max:500',
            'bukti'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('bukti')->store('bukti_izin', 'public');

        Izin::create([
            'user_id'        => auth()->id(),
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'tanggal'        => $request->tanggal,
            'kategori'       => $request->kategori,
            'alasan'         => $request->alasan,
            'bukti_path'     => $path,
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim!');
    }

    // Dipakai oleh Admin untuk menyetujui / menolak
    public function updateStatus(Request $request, Izin $izin)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $izin->update(['status' => $request->status]);

        // Jika disetujui, otomatis buat/update catatan presensi menjadi Sakit / Izin
        if ($request->status === 'approved') {
            Presensi::updateOrCreate(
                [
                    'user_id'        => $izin->user_id,
                    'mata_kuliah_id' => $izin->mata_kuliah_id,
                    'tanggal'        => $izin->tanggal,
                ],
                [
                    'status' => $izin->kategori, // status presensi jadi 'sakit' atau 'izin'
                ]
            );
        }

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}