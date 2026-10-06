<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Tabel pivot many-to-many: mahasiswa (user) <-> mata kuliah yang diambil.
 * Nama pivot mengikuti konvensi Laravel: nama dua model, urut abjad, huruf kecil,
 * bentuk tunggal, dipisah underscore  =>  mata_kuliah_user
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'mata_kuliah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_user');
    }
};
