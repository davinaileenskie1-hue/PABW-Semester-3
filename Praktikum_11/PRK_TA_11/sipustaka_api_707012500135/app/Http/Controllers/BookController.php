<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookController extends Controller
{
    // GET /api/books - semua buku (publik)
    public function index(): JsonResponse
    {
        return response()->json(Book::latest()->get());
    }

    // POST /api/books - tambah buku (wajib login, boleh dengan sampul)
    public function store(StoreBookRequest $request): JsonResponse
    {
        $data = $request->safe()->only(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok']);

        // Jika ada sampul, simpan ke storage/app/private/covers
        if ($request->hasFile('cover')) {
            $data = [...$data, ...$this->storeCover($request->file('cover'))];
        }

        $book = Book::create($data);

        return response()->json($book, 201);
    }

    // GET /api/books/{id} - satu buku (publik)
    public function show(int|string $id): JsonResponse
    {
        return response()->json($this->findBook($id));
    }

    // PATCH /api/books/{id} - ubah buku (wajib login)
    // Jika mengirim sampul, gunakan POST + field _method=PATCH
    public function update(UpdateBookRequest $request, int|string $id): JsonResponse
    {
        $book = $this->findBook($id);
        $data = $request->safe()->only(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok']);

        if ($request->hasFile('cover')) {
            // Ganti sampul lama dengan sampul baru
            $this->deleteCover($book);
            $data = [...$data, ...$this->storeCover($request->file('cover'))];
        } elseif ($request->boolean('remove_cover')) {
            // Hapus sampul tanpa mengganti
            $this->deleteCover($book);
            $data = [...$data, 'cover_path' => null, 'cover_name' => null];
        }

        $book->update($data);

        return response()->json($book);
    }

    // DELETE /api/books/{id} - hapus buku beserta sampulnya (wajib login)
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $book = $this->findBook($id);
        $this->deleteCover($book);
        $book->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }

    // GET /api/books/{id}/cover - membuka sampul buku (publik)
    public function cover(int|string $id): StreamedResponse
    {
        $book = $this->findBook($id);

        if (! $book->cover_path || ! Storage::disk('local')->exists($book->cover_path)) {
            abort(404, 'Sampul tidak ditemukan');
        }

        // inline = gambar langsung dibuka di browser
        return Storage::disk('local')->response(
            $book->cover_path,
            $book->cover_name,
            [],
            'inline'
        );
    }

    // Mencari buku, jika tidak ada kirim 404
    private function findBook(int|string $id): Book
    {
        $book = Book::find($id);
        if (! $book) {
            abort(404, 'Buku tidak ditemukan');
        }

        return $book;
    }

    // Simpan sampul ke folder covers, kembalikan lokasi dan nama aslinya
    private function storeCover(UploadedFile $file): array
    {
        return [
            'cover_path' => $file->store('covers', 'local'),
            'cover_name' => $file->getClientOriginalName(),
        ];
    }

    // Hapus sampul lama dari storage (jika ada)
    private function deleteCover(Book $book): void
    {
        if ($book->cover_path) {
            Storage::disk('local')->delete($book->cover_path);
        }
    }
}
