<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;   // Untuk mengelola (menghapus) file di storage

class LaporanController extends Controller
{
    // READ: menampilkan daftar laporan beserta thumbnail foto dan pagination
    public function index()
    {
        $laporan = Laporan::latest()->paginate(5);
        return view('laporan.index', compact('laporan'));
    }

    // CREATE: menampilkan form pelaporan warga
    public function create()
    {
        return view('laporan.create');
    }

    // CREATE: memvalidasi dan menyimpan laporan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelapor' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'lokasi'       => 'required|string|max:255',
            'kecamatan'    => 'required|string|max:255',
            'tinggi_air'   => 'required|integer|min:0',
            'keterangan'   => 'nullable|string',
            'foto'         => 'required|image|mimes:jpg,jpeg,png|max:2048', // maks. 2 MB
        ]);

        $foto = $request->file('foto');              // Ambil foto dari form
        $path = $foto->store('laporan', 'public');   // Simpan ke storage/app/public/laporan

        Laporan::create([
            'nama_pelapor' => $request->nama_pelapor,
            'no_hp'        => $request->no_hp,
            'lokasi'       => $request->lokasi,
            'kecamatan'    => $request->kecamatan,
            'tinggi_air'   => $request->tinggi_air,
            'keterangan'   => $request->keterangan,
            'foto'         => $path,                 // Path foto di storage
            'status'       => 'Menunggu',            // Status bawaan
        ]);

        return redirect()->route('laporan.create')->with('success', 'Laporan berhasil dikirim.');
    }

    // READ: menampilkan detail satu laporan
    public function show(Laporan $laporan)
    {
        return view('laporan.show', compact('laporan'));
    }

    // UPDATE: menampilkan form edit laporan
    public function edit(Laporan $laporan)
    {
        return view('laporan.edit', compact('laporan'));
    }

    // UPDATE: menyimpan perubahan (foto baru bersifat opsional)
    public function update(Request $request, Laporan $laporan)
    {
        $request->validate([
            'nama_pelapor' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'lokasi'       => 'required|string|max:255',
            'kecamatan'    => 'required|string|max:255',
            'tinggi_air'   => 'required|integer|min:0',
            'keterangan'   => 'nullable|string',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'       => 'required|in:Menunggu,Diproses,Selesai',
        ]);

        $data = $request->only(
            'nama_pelapor',
            'no_hp',
            'lokasi',
            'kecamatan',
            'tinggi_air',
            'keterangan',
            'status'
        );

        if ($request->hasFile('foto')) {                        // Jika petugas memilih foto baru
            Storage::disk('public')->delete($laporan->foto);    // Hapus foto lama
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan->update($data);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    // DELETE: menghapus foto di storage dan data di database
    public function destroy(Laporan $laporan)
    {
        Storage::disk('public')->delete($laporan->foto); // Hapus file fisik
        $laporan->delete();                              // Hapus data di tabel laporan

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
