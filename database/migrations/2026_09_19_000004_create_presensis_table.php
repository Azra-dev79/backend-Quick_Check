<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Catatan presensi. Data nim/nama/kelas/jurusan disimpan sebagai "snapshot"
 * agar riwayat tetap utuh walaupun data induk mahasiswa berubah.
 * Kombinasi (nim, mata_kuliah_id, tanggal) dibuat UNIQUE => mustahil absen dobel
 * di hari yang sama, sekalipun ada dua request masuk bersamaan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20)->index();
            $table->string('nama', 100);
            $table->string('kelas', 10)->nullable();
            $table->string('jurusan', 100)->nullable();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->string('status', 10)->default('Hadir');
            $table->date('tanggal')->index();
            
            // --- MODIFIKASI / TAMBAHAN DI SINI ---
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->dateTime('waktu_hadir')->nullable(); // atau bisa dipertahankan sebagai timestamp awal
            // -------------------------------------

            $table->string('sumber', 10)->default('kelas'); // kelas = scan QR kelas, admin = dicatat admin
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['nim', 'mata_kuliah_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
