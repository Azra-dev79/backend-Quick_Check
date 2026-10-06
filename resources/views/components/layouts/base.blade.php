@props(['title' => 'QuickCheck'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        // Terapkan tema tersimpan sebelum halaman digambar agar tidak berkedip.
        try { document.documentElement.setAttribute('data-theme', localStorage.getItem('qc-theme') || 'light'); } catch (e) {}
    </script>
</head>
<body>
    {{ $slot }}

    <div id="toastWrap"></div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

    {{-- Pesan flash dari controller: ->with('status', ...) / ->with('error', ...) / $errors --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('status'))
                toast('success', @js(session('status')), '', 9000);
            @endif
            @if (session('error'))
                toast('error', @js(session('error')), '', 7000);
            @endif
            @if ($errors->any())
                toast('error', 'Periksa kembali isian', @js($errors->first()), 7000);
            @endif
        });
    </script>
</body>
</html>
