<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Buku\Models\Buku;

test('guests are redirected to the login page', function () {
    $this->get('/buku')->assertRedirect('/login');
    $this->get('/buku/create')->assertRedirect('/login');
});

test('regular users can view the book list but cannot manage books', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/buku')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Buku::index', shouldExist: false));

    $this->actingAs($user)->get('/buku/create')->assertForbidden();
    $this->actingAs($user)->post('/buku', [])->assertForbidden();
});

test('an admin can create a book', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->post('/buku', [
        'judul' => 'Belajar Laravel',
        'penulis' => 'Andi',
        'penerbit' => 'Informatika',
        'tahun_terbit' => 2024,
        'stok' => 5,
    ])->assertRedirect(route('buku.index'));

    $buku = Buku::sole();

    expect($buku->judul)->toBe('Belajar Laravel')
        ->and($buku->tahun_terbit)->toBe(2024)
        ->and($buku->stok)->toBe(5);
});

test('creating a book validates the input', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->post('/buku', [
        'judul' => '',
        'penulis' => '',
        'penerbit' => '',
        'tahun_terbit' => 1800,
        'stok' => -1,
    ])->assertSessionHasErrors(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok']);

    expect(Buku::count())->toBe(0);
});

test('the book list can be searched and paginated', function () {
    $this->actingAs(User::factory()->create());

    Buku::factory()->create(['judul' => 'Laravel untuk Pemula']);
    Buku::factory()->create(['judul' => 'React Dasar']);
    Buku::factory()->count(10)->create();

    $this->get('/buku?search=laravel')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.search', 'laravel')
            ->where('books.data', fn ($books) => collect($books)->pluck('judul')->all() === ['Laravel untuk Pemula'])
        );

    $this->get('/buku')
        ->assertInertia(fn (Assert $page) => $page
            ->has('books.data', 10)
            ->where('books.total', 12)
        );
});

test('an admin can update a book', function () {
    $this->actingAs(User::factory()->admin()->create());

    $buku = Buku::factory()->create();

    $this->put("/buku/{$buku->id}", [
        'judul' => 'Judul Baru',
        'penulis' => 'Penulis Baru',
        'penerbit' => 'Penerbit Baru',
        'tahun_terbit' => 2025,
        'stok' => 20,
    ])->assertRedirect(route('buku.index'));

    expect($buku->fresh())
        ->judul->toBe('Judul Baru')
        ->penulis->toBe('Penulis Baru')
        ->tahun_terbit->toBe(2025)
        ->stok->toBe(20);
});

test('an admin can delete a book', function () {
    $this->actingAs(User::factory()->admin()->create());

    $buku = Buku::factory()->create();

    $this->delete("/buku/{$buku->id}")->assertRedirect(route('buku.index'));

    expect(Buku::find($buku->id))->toBeNull();
});

test('guests cannot open the book management routes', function () {
    $buku = Buku::factory()->create();

    $this->get("/buku/{$buku->id}/edit")->assertRedirect('/login');
    $this->put("/buku/{$buku->id}", [])->assertRedirect('/login');
    $this->delete("/buku/{$buku->id}")->assertRedirect('/login');
});
