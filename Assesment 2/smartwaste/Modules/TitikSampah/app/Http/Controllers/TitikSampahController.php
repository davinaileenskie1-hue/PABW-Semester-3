<?php

namespace Modules\Fasilitas\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\TitikSampah\Models\TitikSampah;
use Modules\TitikSampah\Http\Requests\TitikSampahRequest;
use Modules\TitikSampah\Models\TitikSampah as ModelsTitikSampah;

class TitikSampahController extends Controller
{
    /**
     * Daftar fasilitas + fitur pencarian, filter kondisi, dan pagination.
     */
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $kondisi = $request->query('kondisi');

        $TitikSampah = TitikSampah::query()
            ->search($keyword)
            ->kondisi($kondisi)
            ->withCount('pengaduan')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('TitikSampah::index', [
            'TitikSampah' => $TitikSampah,
            'keyword' => $keyword,
            'kondisi' => $kondisi,
            'rekap' => [
                'total' => ModelsTitikSampah::count(),
                'baik' => ModelsTitikSampah::where('kondisi', 'baik')->count(),
                'rusak' => ModelsTitikSampah::where('kondisi', '!=', 'baik')->count(),
            ],
        ]);
    }

    /**
     * Form tambah fasilitas.
     */
    public function create(): View
    {
        return view('fasilitas::create', ['fasilitas' => new TitikSampah()]);
    }

    /**
     * Simpan fasilitas baru.
     */
    public function store(TitikSampahRequest $request): RedirectResponse
    {
        $TitikSampah = TitikSampah::create($request->validated());

        return redirect()
            ->route('fasilitas.index')
            ->with('success', 'Fasilitas "' . $TitikSampah->nama . '" berhasil ditambahkan.');
    }

    /**
     * Detail fasilitas beserta pengaduan terkait.
     */
    public function show(TitikSampah $TitikSampah): View
    {
        $TitikSampah->load([
            'pengaduan' => fn ($q) => $q->with('user')->latest('tanggal_pengaduan')->limit(10),
        ]);

        return view('TitikSampah::show', [
            'TitikSampah' => $TitikSampah,
            'totalPengaduan' => $TitikSampah->pengaduan()->count(),
        ]);
    }

    /**
     * Form edit fasilitas.
     */
    public function edit(TitikSampah $fasilitas): View
    {
        return view('fasilitas::edit', compact('fasilitas'));
    }

    /**
     * Update data fasilitas.
     */
    public function update(TitikSampahRequest $request, TitikSampah $TitikSampah): RedirectResponse
    {
        $TitikSampah->update($request->validated());

        return redirect()
            ->route('TitikSampah.index')
            ->with('success', 'TitikSampah "' . $TitikSampah->nama . '" berhasil diperbarui.');
    }

    /**
     * Hapus fasilitas (pengaduan terkait ikut terhapus lewat cascade).
     */
    public function destroy(TitikSampah $TitikSampah): RedirectResponse
    {
        $nama = $TitikSampah->nama;
        $TitikSampah->delete();

        return redirect()
            ->route('TitikSampah.index')
            ->with('success', 'TitikSampah "' . $nama . '" berhasil dihapus.');
    }
}
