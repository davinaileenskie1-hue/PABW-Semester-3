<?php

namespace Modules\Laporan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaporanRequest extends FormRequest
{
    /**
     * Pengaduan dibuat oleh mahasiswa yang sedang login.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->isMahasiswa();
    }

    /**
     * Aturan validasi form pengaduan.
     */
    public function rules(): array
    {
        return [
            'fasilitas_id' => ['required', 'integer', Rule::exists('fasilitas', 'id')],
            'judul' => ['required', 'string', 'min:5', 'max:150'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:1000'],
            'tanggal_pengaduan' => ['required', 'date', 'before_or_equal:today'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Pesan validasi versi Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'fasilitas_id.required' => 'Fasilitas wajib dipilih.',
            'fasilitas_id.exists' => 'Fasilitas yang dipilih tidak ditemukan.',
            'judul.required' => 'Judul pengaduan wajib diisi.',
            'judul.min' => 'Judul pengaduan minimal 5 karakter.',
            'judul.max' => 'Judul pengaduan maksimal 150 karakter.',
            'deskripsi.required' => 'Deskripsi kerusakan wajib diisi.',
            'deskripsi.min' => 'Deskripsi minimal 10 karakter agar petugas memahami masalah.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'tanggal_pengaduan.required' => 'Tanggal pengaduan wajib diisi.',
            'tanggal_pengaduan.date' => 'Tanggal pengaduan tidak valid.',
            'tanggal_pengaduan.before_or_equal' => 'Tanggal pengaduan tidak boleh melebihi hari ini.',
            'foto.image' => 'Foto kerusakan harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'fasilitas_id' => 'fasilitas',
            'judul' => 'judul pengaduan',
            'deskripsi' => 'deskripsi',
            'kapasitas_kg' => 'kapasitas_kg',
            'foto' => 'foto',
        ];
    }
}
