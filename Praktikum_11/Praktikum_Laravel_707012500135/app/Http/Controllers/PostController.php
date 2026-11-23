<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Post::latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->safe()->only(['title', 'content']);

        if ($request->hasFile('file')) {
            $data = [...$data, ...$this->storeFile($request->file('file'))];
        }

        $post = $request->user()->posts()->create($request->validated());
        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($this->findPost($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id): JsonResponse
    {
        $post = $this->findOwnedPost($request, $id);
        $data = $request->safe()->only(['title', 'content']);

        if ($request->hasFile('file')) {
            // Ganti file lama dengan file baru 
            $this->deleteFile($post);
            $data = [...$data, ...$this->storeFile($request->file('file'))];
        } elseif ($request->boolean('remove_file')) {
            // Hapus lampiran tanpa mengganti 
            $this->deleteFile($post);
            $data = [...$data, 'file_path' => null, 'file_name' => null];
        }

        $post->update($data);

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $post = $this->findOwnedPost($request, $id);
        $this->deleteFile($post);
        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus']);
    }

    public function findPost(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            abort(404, 'Post tidak ditemukan');
        }
        return $post;
    }

    private function findOwnedPost(Request $request, string $id)
    {
        $post = $this->findPost($id);
        if ($post->user_id != $request->user()->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah post ini');
        }
        return $post;
    }

    // GET /api/posts/{id}/file - membuka / mengunduh file lampiran 
    public function file(string $id): StreamedResponse
    {
        $post = $this->findPost($id);

        if (! $post->file_path || ! Storage::disk('local')->exists($post->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        // inline = gambar / PDF langsung dibuka di browser 
        return Storage::disk('local')->response(
            $post->file_path,
            $post->file_name,
            [],
            'inline'
        );
    }

    // Simpan file ke folder posts, kembalikan lokasi dan nama aslinya 
    private function storeFile(UploadedFile $file): array
    {
        return [
            'file_path' => $file->store('posts', 'local'),
            'file_name' => $file->getClientOriginalName(),
        ];
    }

    // Hapus file lampiran lama dari storage (jika ada) 
    private function deleteFile(Post $post): void
    {
        if ($post->file_path) {
            Storage::disk('local')->delete($post->file_path);
        }
    }
}