<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Dipakai untuk tambah (store) sekaligus ubah (update) mata kuliah. */
class MataKuliahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'kode' => [
                'required', 'string', 'max:20',
                // Saat update, kode milik record itu sendiri dikecualikan dari pengecekan unik.
                Rule::unique('mata_kuliahs', 'kode')->ignore($this->route('matkul')),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'sks' => ['required', 'integer', 'between:1,6'],
            'dosen' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }
}
