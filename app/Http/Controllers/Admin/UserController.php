<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('mataKuliahs')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $user->delete();

        return back()->with('status', "Akun {$user->username} dihapus.");
    }

    /** Reset password: dibuat acak, ditampilkan sekali ke admin untuk diberikan ke mahasiswa. */
    public function resetPassword(User $user): RedirectResponse
    {
        $baru = Str::password(8, symbols: false);

        $user->update(['password' => $baru]);

        return back()->with('status', "Password baru untuk {$user->username}: {$baru} (catat sekarang, tidak ditampilkan lagi).");
    }
}
