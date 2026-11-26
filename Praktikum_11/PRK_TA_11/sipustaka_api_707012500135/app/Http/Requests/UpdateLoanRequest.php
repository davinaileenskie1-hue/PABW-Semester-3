<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => ['sometimes', 'required', 'integer', 'exists:books,id'],
            'tanggal_pinjam' => ['sometimes', 'required', 'date'],
            // Bandingkan dengan tanggal_pinjam hanya jika field itu ikut dikirim
            'tanggal_kembali' => $this->has('tanggal_pinjam')
                ? ['nullable', 'date', 'after_or_equal:tanggal_pinjam']
                : ['nullable', 'date'],
            'status' => ['sometimes', 'in:Dipinjam,Dikembalikan'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.integer' => 'ID buku harus berupa angka.',
            'book_id.exists' => 'Buku tidak ditemukan.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.date' => 'Tanggal pinjam harus berupa tanggal yang valid.',
            'tanggal_kembali.date' => 'Tanggal kembali harus berupa tanggal yang valid.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh lebih awal dari tanggal pinjam.',
            'status.in' => 'Status hanya boleh Dipinjam atau Dikembalikan.',
        ];
    }
}
