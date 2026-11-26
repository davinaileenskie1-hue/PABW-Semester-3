<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    // GET /api/loans - semua peminjaman milik pengguna yang login
    public function index(Request $request): JsonResponse
    {
        $loans = $request->user()->loans()->with('book')->latest()->get();

        return response()->json($loans);
    }

    // POST /api/loans - catat peminjaman baru
    public function store(StoreLoanRequest $request): JsonResponse
    {
        $data = $request->safe()->only(['book_id', 'tanggal_pinjam', 'tanggal_kembali', 'status']);

        // user_id otomatis diisi dengan ID pengguna yang login
        $loan = $request->user()->loans()->create($data);
        $loan->load('book');

        return response()->json($loan, 201);
    }

    // GET /api/loans/{id} - satu peminjaman (hanya milik sendiri)
    public function show(Request $request, int|string $id): JsonResponse
    {
        return response()->json($this->findOwnedLoan($request, $id)->load('book'));
    }

    // PATCH /api/loans/{id} - ubah peminjaman milik sendiri
    public function update(UpdateLoanRequest $request, int|string $id): JsonResponse
    {
        $loan = $this->findOwnedLoan($request, $id);
        $data = $request->safe()->only(['book_id', 'tanggal_pinjam', 'tanggal_kembali', 'status']);

        $loan->update($data);

        return response()->json($loan->load('book'));
    }

    // DELETE /api/loans/{id} - hapus peminjaman milik sendiri
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $this->findOwnedLoan($request, $id)->delete();

        return response()->json(['message' => 'Peminjaman berhasil dihapus']);
    }

    // Mencari peminjaman, jika tidak ada kirim 404
    private function findLoan(int|string $id): Loan
    {
        $loan = Loan::find($id);
        if (! $loan) {
            abort(404, 'Peminjaman tidak ditemukan');
        }

        return $loan;
    }

    // Hanya pemilik peminjaman yang boleh lanjut
    private function findOwnedLoan(Request $request, int|string $id): Loan
    {
        $loan = $this->findLoan($id);
        if ($loan->user_id != $request->user()->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah peminjaman ini');
        }

        return $loan;
    }
}
