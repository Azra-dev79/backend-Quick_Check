<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Tempat mendaftarkan service ke container (tidak dipakai di proyek ini). */
    public function register(): void
    {
        //
    }

    /** Dijalankan setelah semua service siap: tempat pengaturan global aplikasi. */
    public function boot(): void
    {
        // Tampilan pagination sendiri (tanpa Tailwind).
        Paginator::defaultView('pagination.qc');

        // Nama hari/bulan berbahasa Indonesia pada translatedFormat().
        Carbon::setLocale(config('app.locale'));
    }
}
