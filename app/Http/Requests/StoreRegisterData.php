<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterData extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string|max:50|min:3',
            'email_perusahaan' => 'required|email|min:6',
            'no_telp_perusahaan' => 'required|string|max:13',
            'npwp_perusahaan' => [
                'required',
                'string',
                'size:20',
            ],
            'username' => [
                'required',
                'min:6',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/'
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=,.\/\\|{}[\];:\'"])[A-Za-z\d!@#$%^&*()_\-+=,.\/\\|{}[\];:\'"]{8,}$/'
            ],
            'alamat' => 'required|string|min:10',
            'provinsi_id' => 'required|string',
            'kota_id' => 'required|string',
            'kecamatan_id' => 'required|string',
            'kelurahan_id' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan harus diisi.',
            'nama_perusahaan.string' => 'Nama perusahaan harus berupa string.',
            'nama_perusahaan.max' => 'Nama perusahaan maksimal 50 karakter.',
            'nama_perusahaan.min' => 'Nama perusahaan minimal 3 karakter.',
            'email_perusahaan.required' => 'Email perusahaan harus diisi.',
            'email_perusahaan.email' => 'Email perusahaan tidak valid.',
            'email_perusahaan.min' => 'Email perusahaan minimal 6 karakter.',
            'no_telp_perusahaan.required' => 'Nomor telepon perusahaan harus diisi.',
            'no_telp_perusahaan.string' => 'Nomor telepon perusahaan harus berupa string.',
            'no_telp_perusahaan.max' => 'Nomor telepon perusahaan maksimal 13 karakter.',
            'npwp_perusahaan.required' => 'NPWP perusahaan harus diisi.',
            'npwp_perusahaan.size' => 'NPWP perusahaan harus 20 karakter.',
            'username.required' => 'Username harus diisi.',
            'username.min' => 'Username minimal 6 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh terdiri dari huruf, angka, dan underscore.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa string.',
            'alamat.min' => 'Alamat minimal 10 karakter.',
            'provinsi_id.required' => 'Provinsi ID harus diisi.',
            'kota_id.required' => 'Kota ID harus diisi.',
            'kecamatan_id.required' => 'Kecamatan ID harus diisi.',
            'kelurahan_id.required' => 'Kelurahan ID harus diisi.',
        ];
    }
}
