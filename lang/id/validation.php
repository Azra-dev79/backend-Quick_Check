<?php

/*
| Pesan validasi berbahasa Indonesia.
| Laravel mencari pesan di lang/{locale}/validation.php sesuai APP_LOCALE=id.
*/

return [
    'between' => [
        'numeric' => ':Attribute harus di antara :min dan :max.',
        'file' => ':Attribute harus berukuran antara :min dan :max KB.',
        'string' => ':Attribute harus terdiri dari :min sampai :max karakter.',
    ],
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password saat ini salah.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'after_or_equal' => ':Attribute harus berupa tanggal setelah atau sama dengan :date.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa berkas.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
        'file' => ':Attribute tidak boleh lebih dari :max KB.',
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
    ],
    'mimes' => ':Attribute harus berupa berkas bertipe: :values.',
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'file' => ':Attribute minimal berukuran :min KB.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'required_without' => ':Attribute wajib diisi bila :values tidak diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',

    'attributes' => [
        'nim' => 'NIM',
        'nama' => 'Nama',
        'kelas' => 'Kelas',
        'jurusan' => 'Jurusan',
        'username' => 'Username',
        'password' => 'Password',
        'kode' => 'Kode',
        'sks' => 'SKS',
        'dosen' => 'Dosen',
        'deskripsi' => 'Deskripsi',
        'mata_kuliah_id' => 'Mata kuliah',
        'payload' => 'QR',
        'file' => 'Berkas',
        'dari' => 'Tanggal awal',
        'sampai' => 'Tanggal akhir',
        'konfirmasi' => 'Konfirmasi',
        'current_password' => 'Password saat ini',
    ],
];
