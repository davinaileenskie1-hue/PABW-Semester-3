<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Halaman daftar laporan banjir.
     * Data diambil dari database menggunakan Eloquent (Laporan::all()).
     */
    public function index()
    {
        $laporans = Laporan::all();

        return view('laporan.index', compact('laporans'));
    }

    /**
     * Halaman form pelaporan banjir.
     */
    public function create()
    {
        return view('laporan.form');
    }

    /**
     * Memproses data form yang dikirim melalui method POST,
     * lalu menyimpannya ke tabel laporan menggunakan Laporan::create().
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelapor'     => 'required|string|max:100',
            'lokasi'           => 'required|string|max:150',
            'tinggi_genangan'  => 'required|numeric|min:0',
            'tanggal_kejadian' => 'required|date',
        ]);

        // Menyimpan data ke database melalui Eloquent.
        $laporan = Laporan::create($validated);

        return redirect()
            ->route('laporan.konfirmasi', $laporan)
            ->with('success', 'Laporan berhasil disimpan ke database.');
    }

    /**
     * Halaman konfirmasi yang menampilkan kembali data laporan
     * yang baru saja tersimpan di database.
     */
    public function konfirmasi(Laporan $laporan)
    {
        return view('laporan.konfirmasi', compact('laporan'));
    }
}
