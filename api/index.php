<?php

// Paksa direktori storage dan bootstrap cache langsung ke /tmp SEBELUM Laravel dimuat
putenv('APP_STORAGE=/tmp');
$_ENV['APP_STORAGE'] = '/tmp';

$tmpStorage = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap/cache';

// Buat foldernya jika belum ada di /tmp
foreach ([$tmpStorage, $tmpStorage.'/framework/cache', $tmpStorage.'/framework/sessions', $tmpStorage.'/framework/views', $tmpStorage.'/logs', $tmpBootstrap] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Override path storage bawaan Laravel ke /tmp
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Paksa path instance Laravel ke /tmp
$app->useStoragePath($tmpStorage);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);