<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Dipakai untuk tambah (store) sekaligus ubah (update) data mahasiswa. */
class MahasiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'kelas' => ['required', 'string', 'max:10'],
            'jurusan' => ['required', 'string', 'max:100'],
        ];

        // NIM hanya divalidasi saat tambah data. Saat ubah, NIM tidak boleh diganti
        // karena dipakai sebagai penghubung ke akun dan riwayat presensi.
        if (! $this->route('mahasiswa')) {
            $rules['nim'] = ['required', 'string', 'min:3', 'max:20', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:mahasiswas,nim'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nim.regex' => 'NIM hanya boleh berisi huruf, angka, dan tanda hubung.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
        ];
    }
}
