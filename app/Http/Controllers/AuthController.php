<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auth::attempt mencocokkan username + role, lalu memeriksa password (hash) otomatis.
        $berhasil = Auth::attempt([
            'username' => $data['username'],
            'password' => $data['password'],
            'role' => $data['role'],
        ], $request->boolean('remember'));

        if (! $berhasil) {
            return back()
                ->withErrors(['username' => __('auth.failed')])
                ->onlyInput('username', 'role');
        }

        // Ganti ID session setelah login untuk mencegah session fixation.
        $request->session()->regenerate();

        $beranda = $request->user()->isAdmin() ? route('admin.dashboard') : route('absensi');

        return redirect()->intended($beranda);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Dua tabel diisi sekaligus; transaction memastikan keduanya berhasil atau keduanya batal.
        $mhs = DB::transaction(function () use ($data) {
            // Bila NIM sudah ada di data induk, datanya dipakai; bila belum, dibuat dari isian form.
            $mhs = Mahasiswa::firstOrCreate(
                ['nim' => $data['nim']],
                [
                    'nama' => trim($data['nama']),
                    'kelas' => trim($data['kelas']),
                    'jurusan' => trim($data['jurusan']),
                ]
            );

            $user = new User([
                'name' => $mhs->nama,
                'username' => $mhs->nim,   // username mahasiswa = NIM
                'nim' => $mhs->nim,
                'kelas' => $mhs->kelas,
                'jurusan' => $mhs->jurusan,
                'password' => $data['password'],
            ]);
            $user->role = User::ROLE_MAHASISWA; // role tidak mass-assignable, diisi eksplisit
            $user->save();

            return $mhs;
        });

        return redirect()
            ->route('login')
            ->with('status', "Akun untuk {$mhs->nama} berhasil dibuat. Silakan masuk.")
            ->withInput(['username' => $mhs->nim, 'role' => User::ROLE_MAHASISWA]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();      // hapus seluruh isi session
        $request->session()->regenerateToken(); // token CSRF baru

        return redirect()->route('login');
    }
}
