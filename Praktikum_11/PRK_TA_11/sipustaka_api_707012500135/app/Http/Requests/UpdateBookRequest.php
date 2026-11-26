<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'judul' => ['sometimes', 'required', 'string', 'max:200'],
            'penulis' => ['sometimes', 'required', 'string', 'max:150'],
            'penerbit' => ['sometimes', 'required', 'string', 'max:150'],
            'tahun_terbit' => ['sometimes', 'required', 'integer', 'min:1900', 'max:'.date('Y')],
            'stok' => ['sometimes', 'required', 'integer', 'min:0'],
            // Kirim file baru untuk mengganti sampul lama
            'cover' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png'],
            // Kirim remove_cover=1 untuk menghapus sampul
            'remove_cover' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.max' => 'Judul buku maksimal 200 karakter.',
            'penulis.required' => 'Nama penulis wajib diisi.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.min' => 'Tahun terbit tidak boleh kurang dari 1900.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun sekarang.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh bernilai negatif.',
            'cover.max' => 'Ukuran sampul maksimal 2 MB.',
            'cover.mimes' => 'Format sampul harus jpg, jpeg, atau png.',
            'cover.uploaded' => 'Sampul gagal diunggah, periksa ukuran file.',
        ];
    }
}
