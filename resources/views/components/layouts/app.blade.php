@props(['title' => 'QuickCheck'])
<x-layouts.base :title="$title">

    <style>
        .custom-topbar {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 12px 16px;
        }
        .topbar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .icon-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.2);
            background: transparent;
            color: inherit;
        }
    </style>

    <header class="topbar">
        <div class="topbar-in custom-topbar">
            
            <!-- Baris Atas: Logo & Tombol Ikon/Logout -->
            <div class="topbar-row">
                <a class="brand" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('absensi') }}">
                    <i class="fas fa-fingerprint"></i> QUICKCHECK
                </a>

                <div class="userbox" style="display: flex; align-items: center; gap: 8px; margin: 0; padding: 0; background: none; border: none;">
                    <!-- Tombol Ganti Tema -->
                    <button type="button" class="iconbtn icon-action-btn" id="themeBtn" onclick="toggleTheme()" title="Ganti Tema">
                        <i class="fas fa-moon" style="font-size: 14px;"></i>
                    </button>

                    <!-- Tombol Logout -->
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0; display: inline-flex;">
                        @csrf
                        <button type="submit" class="iconbtn icon-action-btn" title="Keluar" style="color: #fca5a5;">
                            <i class="fas fa-right-from-bracket" style="font-size: 14px;"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Baris Bawah: Nama User & Menu Navigasi -->
            <div class="topbar-row" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; flex-wrap: wrap; gap: 8px;">
                <span class="who" style="font-size: 13px; opacity: 0.9;">
                    <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                </span>

                <nav class="nav" style="display: flex; gap: 6px; margin: 0;">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" @class(['on' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                        <a href="{{ route('admin.mahasiswa.index') }}" @class(['on' => request()->routeIs('admin.mahasiswa.*')])>Mahasiswa</a>
                        <a href="{{ route('admin.matkul.index') }}" @class(['on' => request()->routeIs('admin.matkul.*')])>Mata Kuliah</a>
                        <a href="{{ route('admin.presensi.index') }}" @class(['on' => request()->routeIs('admin.presensi.*')])>Presensi</a>
                        <a href="{{ route('admin.qr.index') }}" @class(['on' => request()->routeIs('admin.qr.*')])>QR Code</a>
                        <a href="{{ route('admin.scan') }}" @class(['on' => request()->routeIs('admin.scan')])>Scan</a>
                        <a href="{{ route('admin.users.index') }}" @class(['on' => request()->routeIs('admin.users.*')])>Akun</a>
                    @else
                        <a href="{{ route('absensi') }}" @class(['on' => request()->routeIs('absensi')]) style="padding: 6px 12px; font-size: 13px; border-radius: 6px;">
                            <i class="fas fa-qrcode"></i> Presensi
                        </a>
                        <a href="{{ route('profil') }}" @class(['on' => request()->routeIs('profil')]) style="padding: 6px 12px; font-size: 13px; border-radius: 6px;">
                            <i class="fas fa-user"></i> Profil
                        </a>
                    @endif
                </nav>
            </div>

        </div>
    </header>

    <main class="container">
        {{ $slot }}
    </main>
</x-layouts.base>