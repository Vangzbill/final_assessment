<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CpRequest extends FormRequest
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
            'customer_id' => ['required', 'exists:tbl_customer,id'],
            'nama' => ['required', 'string'],
            'email' => ['required', 'email'],
            'no_telp' => ['required', 'string'],
        ];
    }
}
