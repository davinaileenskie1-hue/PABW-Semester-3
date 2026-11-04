<?php

use App\Enums\UserRole;
use App\Models\User;
use Modules\Buku\Models\Buku;

it('menampilkan katalog buku untuk user tanpa tombol tambah', function () {
    $user = User::factory()->create();
    Buku::factory()->count(3)->create();

    $this->actingAs($user)->get('/buku')
        ->assertOk()
        ->assertSee('Katalog Buku')
        ->assertDontSee('Tambah Buku');
});

it('menampilkan tombol tambah untuk admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/buku')->assertOk()->assertSee('Tambah Buku');
});

it('menyimpan buku baru oleh admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/buku', [
        'judul' => 'Pemrograman Berorientasi Objek',
        'penulis' => 'Adi Nugroho',
        'penerbit' => 'Andi',
        'tahun_terbit' => 2020,
        'stok' => 4,
    ])->assertRedirect('/buku')->assertSessionHas('success');

    $this->assertDatabaseHas('bukus', ['judul' => 'Pemrograman Berorientasi Objek', 'stok' => 4]);
});

it('menolak buku dengan data tidak valid', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->from('/buku/create')->post('/buku', [
        'judul' => '',
        'penulis' => '',
        'penerbit' => '',
        'tahun_terbit' => 1500,
        'stok' => -3,
    ])->assertRedirect('/buku/create')->assertSessionHasErrors(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok']);
});

it('memperbarui dan menghapus buku', function () {
    $admin = User::factory()->admin()->create();
    $buku = Buku::factory()->create(['judul' => 'Judul Lama']);

    $this->actingAs($admin)->put("/buku/{$buku->id}", [
        'judul' => 'Judul Baru',
        'penulis' => $buku->penulis,
        'penerbit' => $buku->penerbit,
        'tahun_terbit' => $buku->tahun_terbit,
        'stok' => 9,
    ])->assertRedirect('/buku');

    expect($buku->fresh()->judul)->toBe('Judul Baru')
        ->and($buku->fresh()->stok)->toBe(9);

    $this->actingAs($admin)->delete("/buku/{$buku->id}")->assertRedirect('/buku');
    $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
});

it('menyaring daftar buku berdasarkan kata kunci', function () {
    $admin = User::factory()->admin()->create();
    Buku::factory()->create(['judul' => 'Algoritma Dasar']);
    Buku::factory()->create(['judul' => 'Basis Data']);

    $this->actingAs($admin)->get('/buku?search=Algoritma')
        ->assertOk()
        ->assertSee('Algoritma Dasar')
        ->assertDontSee('Basis Data');
});
