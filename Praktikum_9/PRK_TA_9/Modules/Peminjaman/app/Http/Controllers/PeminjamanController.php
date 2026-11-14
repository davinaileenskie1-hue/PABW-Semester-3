<?php

namespace Modules\Peminjaman\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Peminjaman\Http\Requests\PeminjamanRequest;
use Modules\Peminjaman\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /**
     * Display the React page. Data is loaded via AJAX.
     */
    public function index(): Response
    {
        return Inertia::render('Peminjaman::index');
    }

    /**
     * Return all loan records as JSON.
     */
    public function getData(): JsonResponse
    {
        return response()->json(Peminjaman::latest()->get());
    }

    /**
     * Store a newly created loan together with its uploaded proof.
     */
    public function store(PeminjamanRequest $request): JsonResponse
    {
        $data = $request->safe()->except('bukti');

        $file = $request->file('bukti');
        $data['bukti'] = $file->store('bukti_peminjaman', 'public');
        $data['nama_bukti'] = $file->getClientOriginalName();

        Peminjaman::create($data);

        return response()->json(['success' => 'Data peminjaman berhasil disimpan.']);
    }

    /**
     * Return a single loan record for the edit form.
     */
    public function edit(Peminjaman $peminjaman): JsonResponse
    {
        return response()->json($peminjaman);
    }

    /**
     * Update the specified loan. The old proof is replaced only when a new file is uploaded.
     */
    public function update(PeminjamanRequest $request, Peminjaman $peminjaman): JsonResponse
    {
        $data = $request->safe()->except('bukti');

        if ($request->hasFile('bukti')) {
            if ($peminjaman->bukti) {
                Storage::disk('public')->delete($peminjaman->bukti);
            }

            $file = $request->file('bukti');
            $data['bukti'] = $file->store('bukti_peminjaman', 'public');
            $data['nama_bukti'] = $file->getClientOriginalName();
        }

        $peminjaman->update($data);

        return response()->json(['success' => 'Data peminjaman berhasil diperbarui.']);
    }

    /**
     * Remove the specified loan and its uploaded proof.
     */
    public function destroy(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->bukti) {
            Storage::disk('public')->delete($peminjaman->bukti);
        }

        $peminjaman->delete();

        return response()->json(['success' => 'Data peminjaman berhasil dihapus.']);
    }
}
