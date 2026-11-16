<?php

namespace Modules\Pengaduan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\TitikSampah\Models\TitikSampah;
use Modules\Laporan\Http\Requests\LaporanRequest;
use Modules\Laporan\Models\Laporan;

class LaporanController extends Controller
{
    /**
     * Daftar pengaduan (server-side render).
     * Mahasiswa hanya melihat pengaduan miliknya sendiri.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $keyword = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $Laporan = Laporan::query()
            ->with(['fasilitas', 'user'])
            ->when($user->isMahasiswa(), fn ($q) => $q->where('user_id', $user->id))
            ->search($keyword)
            ->status($status)
            ->latest('tanggal_pengaduan')
            ->paginate(8)
            ->withQueryString();

        return view('pengaduan::index', [
            'pengaduan' => $Laporan,
            'keyword' => $keyword,
            'status' => $status,
            'fasilitas' => Laporan::query()->orderBy('nama')->get(),
            'canManage' => $user->canManage(),
            'rekap' => [
                'total' => Laporan::when($user->isMahasiswa(), fn ($q) => $q->where('user_id', $user->id))->count(),
                'baru' => Laporan::when($user->isMahasiswa(), fn ($q) => $q->where('user_id', $user->id))->where('status', 'baru')->count(),
                'diproses' => Laporan::when($user->isMahasiswa(), fn ($q) => $q->where('user_id', $user->id))->where('status', 'diproses')->count(),
            ],
        ]);
    }

    /**
     * Simpan pengaduan baru lewat AJAX (JSON + FormData).
     */
    public function store(LaporanRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Pengaduan selalu milik user yang sedang login
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'baru';

        if ($request->hasFile('foto')) {
            // Upload ke storage/app/public/pengaduan
            $data['foto'] = $request->file('foto')->store('pengaduan', 'public');
        }

        $pengaduan = Laporan::create($data)->load(['fasilitas', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan "' . $pengaduan->judul . '" berhasil dikirim.',
            'id' => $pengaduan->id,
            'row' => view('pengaduan::partials.row', [
                'item' => $pengaduan,
                'canManage' => $request->user()->canManage(),
            ])->render(),
        ], 201);
    }

    /**
     * Ubah status pengaduan lewat AJAX (JSON).
     */
    public function update(Request $request, Laporan $pengaduan): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Laporan::STATUS))],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status pengaduan tidak valid.',
        ]);

        $pengaduan->update(['status' => $validated['status']]);
        $pengaduan->load(['fasilitas', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Status pengaduan #' . $pengaduan->id . ' diubah menjadi ' . $pengaduan->status_label . '.',
            'id' => $pengaduan->id,
            'row' => view('pengaduan::partials.row', [
                'item' => $pengaduan,
                'canManage' => $request->user()->canManage(),
            ])->render(),
        ]);
    }

    /**
     * Hapus pengaduan lewat AJAX (JSON) beserta file fotonya.
     */
    public function destroy(Request $request, Laporan $pengaduan): JsonResponse
    {
        if ($pengaduan->foto) {
            Storage::disk('public')->delete($pengaduan->foto);
        }

        $id = $pengaduan->id;
        $pengaduan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan #' . $id . ' berhasil dihapus.',
            'id' => $id,
        ]);
    }
}
