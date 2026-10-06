<?php

// Mengatur folder storage agar mengarah ke /tmp di Vercel
$_ENV['APP_STORAGE'] = $_ENV['APP_STORAGE'] ?? '/tmp';

if (!is_dir('/tmp/storage')) {
    mkdir('/tmp/storage', 0777, true);
    foreach (['framework/views', 'framework/cache', 'framework/sessions', 'logs'] as $dir) {
        if (!is_dir('/tmp/storage/' . $dir)) {
            mkdir('/tmp/storage/' . $dir, 0777, true);
        }
    }
}

// Salin konfigurasi cache jika belum ada
require __DIR__ . '/../public/index.php';