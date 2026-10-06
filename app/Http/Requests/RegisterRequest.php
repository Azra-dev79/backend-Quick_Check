<?php

namespace App\Http\Requests;

use App\Models\Mahasiswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $nim = ['required', 'string', 'min:3', 'max:20', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:users,username', 'unique:users,nim'];

        // Mode tertutup (QC_REGISTER_OPEN=false): NIM wajib sudah ada di data induk.
        if (! config('quickcheck.register_open')) {
            $nim[] = 'exists:mahasiswas,nim';
        }

        return [
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'nim' => $nim,
            'kelas' => ['required', 'string', 'max:10'],
            'jurusan' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:'.config('quickcheck.min_password'), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'nim.exists' => 'NIM belum terdaftar di data mahasiswa. Hubungi admin.',
            'nim.unique' => 'NIM ini sudah memiliki akun. Silakan masuk.',
            'nim.regex' => 'NIM hanya boleh berisi huruf, angka, dan tanda hubung.',
        ];
    }

    /**
     * Bila NIM sudah ada di data induk (mis. hasil impor CSV dari admin), nama yang
     * diketik harus cocok. Ini melindungi data yang sudah didaftarkan admin dari
     * orang yang memakai NIM milik orang lain. NIM baru dibiarkan lolos (mode terbuka).
     *
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $mhs = Mahasiswa::where('nim', $this->input('nim'))->first();

                if ($mhs && mb_strtolower(trim($mhs->nama)) !== mb_strtolower(trim((string) $this->input('nama')))) {
                    $validator->errors()->add('nama', 'Nama tidak sesuai dengan data mahasiswa yang sudah terdaftar untuk NIM tersebut.');
                }
            },
        ];
    }
}
