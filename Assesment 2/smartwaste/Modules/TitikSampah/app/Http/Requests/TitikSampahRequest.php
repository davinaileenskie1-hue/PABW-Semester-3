<?php

namespace Modules\TitikSampah\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\TitikSampah\Http\Controllers\TitikSampahController;
use Modules\TitikSampah\Models\TitikSampah;

class TitikSampahRequest extends FormRequest
{
    /**
     * Hanya petugas yang boleh menyimpan data fasilitas.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->canManage();
    }

    /**
     * Aturan validasi form fasilitas.
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:150'],
            'lokasi' => ['required', 'string', 'min:3', 'max:150'],
            'kategori' => ['required', 'string', Rule::in(TitikSampah::KATEGORI)],
            'kondisi' => ['required', 'string', Rule::in(array_keys(TitikSampah::KONDISI))],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan validasi versi Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama fasilitas wajib diisi.',
            'nama.min' => 'Nama fasilitas minimal 3 karakter.',
            'nama.max' => 'Nama fasilitas maksimal 150 karakter.',
            'lokasi.required' => 'Lokasi fasilitas wajib diisi.',
            'lokasi.min' => 'Lokasi minimal 3 karakter.',
            'lokasi.max' => 'Lokasi maksimal 150 karakter.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori yang dipilih tidak tersedia.',
            'kondisi.required' => 'Kondisi fasilitas wajib dipilih.',
            'kondisi.in' => 'Kondisi fasilitas tidak valid.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }

    /**
     * Nama atribut yang lebih ramah untuk pesan error.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama fasilitas',
            'lokasi' => 'lokasi',
            'kategori' => 'kategori',
            'kondisi' => 'kondisi',
            'deskripsi' => 'deskripsi',
        ];
    }
}
