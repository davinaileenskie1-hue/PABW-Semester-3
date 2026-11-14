<?php

namespace Modules\Buku\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Buku\Http\Requests\StoreBukuRequest;
use Modules\Buku\Http\Requests\UpdateBukuRequest;
use Modules\Buku\Models\Buku;

class BukuController extends Controller
{
    /**
     * Display a listing of books. All authenticated users may open this page.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $books = Buku::query()
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('penerbit', 'like', "%{$search}%");
                });
            })
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Buku::index', [
            'books' => $books,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(): Response
    {
        return Inertia::render('Buku::create');
    }

    /**
     * Store a newly created book.
     */
    public function store(StoreBukuRequest $request): RedirectResponse
    {
        Buku::create($request->validated());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Buku $buku): Response
    {
        return Inertia::render('Buku::edit', [
            'buku' => [
                'id' => $buku->id,
                'judul' => $buku->judul,
                'penulis' => $buku->penulis,
                'penerbit' => $buku->penerbit,
                'tahun_terbit' => $buku->tahun_terbit,
                'stok' => $buku->stok,
            ],
        ]);
    }

    /**
     * Update the specified book.
     */
    public function update(UpdateBukuRequest $request, Buku $buku): RedirectResponse
    {
        $buku->update($request->validated());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
