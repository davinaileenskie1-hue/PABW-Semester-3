<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KontakController extends Controller
{
    public function index()
    {
        $kontaks = Kontak::latest()->paginate(5); // 5 data per halaman
        return view('kontaks.index', compact('kontaks'));
    }

    public function create()
    {
        return view('kontaks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Foto opsional, maks. 2 MB
        ]);

        if ($request->hasFile('foto')) {
            // Simpan ke storage/app/public/foto_kontak, lalu simpan path-nya
            $validated['foto'] = $request->file('foto')->store('foto_kontak', 'public');
        }

        Kontak::create($validated);

        return redirect()->route('kontaks.index')->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function show(Kontak $kontak)
    {
        return view('kontaks.show', compact('kontak'));
    }

    public function edit(Kontak $kontak)
    {
        return view('kontaks.edit', compact('kontak'));
    }

    public function update(Request $request, Kontak $kontak)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($kontak->foto) {
                Storage::disk('public')->delete($kontak->foto); // Hapus foto lama
            }
            $validated['foto'] = $request->file('foto')->store('foto_kontak', 'public');
        }

        $kontak->update($validated);

        return redirect()->route('kontaks.index')->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Kontak $kontak)
    {
        if ($kontak->foto) {
            Storage::disk('public')->delete($kontak->foto); // Hapus file foto
        }
        
        $kontak->delete(); // Hapus data dari database

        return redirect()->route('kontaks.index')->with('success', 'Kontak berhasil dihapus.');
    }
}