<x-layouts.app title="Akun Pengguna">
    <div class="page-head">
        <div class="kicker">// manajemen akun</div>
        <h1>Akun Pengguna</h1>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Username</th><th>Nama</th><th>Peran</th><th>Matkul</th><th>Dibuat</th><th></th></tr></thead>
                <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td class="mono">{{ $u->username }}</td>
                        <td>{{ $u->name }}</td>
                        <td><span class="badge {{ $u->isAdmin() ? 'warn' : '' }}">{{ $u->role }}</span></td>
                        <td>{{ $u->mata_kuliahs_count }}</td>
                        <td class="mono">{{ $u->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('admin.users.reset', $u) }}" data-confirm="Reset password {{ $u->username }}?">
                                    @csrf @method('PUT')
                                    <button class="btn sm" type="submit" title="Reset password"><i class="fas fa-key"></i></button>
                                </form>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus akun {{ $u->username }}?">
                                        @csrf @method('DELETE')
                                        <button class="btn sm danger" type="submit" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px">{{ $users->links() }}</div>
    </div>
</x-layouts.app>
