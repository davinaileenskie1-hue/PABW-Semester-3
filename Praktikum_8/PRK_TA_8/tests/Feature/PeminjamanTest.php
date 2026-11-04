<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Peminjaman\Models\Peminjaman;

beforeEach(function () {
    Storage::fake('public');
});

function dataPeminjaman(array $override = []): array
{
    return array_merge([
        'nama_peminjam' => 'Budi Santoso',
        'judul_buku' => 'Algoritma dan Pemrograman',
        'tanggal_pinjam' => '2026-11-01',
        'tanggal_kembali' => null,
        'status' => 'Dipinjam',
    ], $override);
}

it('mengembalikan data peminjaman sebagai JSON', function () {
    $user = User::factory()->create();
    Peminjaman::factory()->count(3)->create();

    $this->actingAs($user)->getJson('/peminjaman/data')
        ->assertOk()
        ->assertJsonCount(3)
        ->assertJsonStructure([['id', 'nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status', 'bukti_url']]);
});

it('menyimpan peminjaman beserta bukti melalui request JSON', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/peminjaman', dataPeminjaman([
        'bukti' => UploadedFile::fake()->image('bukti.png'),
    ]))->assertOk()->assertJson(['success' => 'Data peminjaman berhasil disimpan.']);

    $peminjaman = Peminjaman::firstOrFail();
    expect($peminjaman->nama_peminjam)->toBe('Budi Santoso')
        ->and($peminjaman->nama_bukti)->toBe('bukti.png');

    Storage::disk('public')->assertExists($peminjaman->bukti);
});

it('mengembalikan 422 ketika data tidak valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/peminjaman', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'status', 'bukti']);
});

it('menolak berkas berformat zip', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/peminjaman', dataPeminjaman([
        'bukti' => UploadedFile::fake()->create('dokumen.zip', 100),
    ]))->assertStatus(422)->assertJsonValidationErrors(['bukti']);
});

it('menolak berkas lebih dari 2 MB', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/peminjaman', dataPeminjaman([
        'bukti' => UploadedFile::fake()->image('besar.png')->size(3000),
    ]))->assertStatus(422)->assertJsonValidationErrors(['bukti']);
});

it('mengganti bukti lama ketika diedit', function () {
    $user = User::factory()->create();
    $peminjaman = Peminjaman::factory()->create([
        'bukti' => 'bukti_peminjaman/lama.png',
        'nama_bukti' => 'lama.png',
    ]);
    Storage::disk('public')->put('bukti_peminjaman/lama.png', 'isi lama');

    $this->actingAs($user)->putJson("/peminjaman/{$peminjaman->id}", dataPeminjaman([
        'status' => 'Dikembalikan',
        'tanggal_kembali' => '2026-11-05',
        'bukti' => UploadedFile::fake()->create('surat.pdf', 200, 'application/pdf'),
    ]))->assertOk();

    $peminjaman->refresh();
    expect($peminjaman->status)->toBe('Dikembalikan')
        ->and($peminjaman->nama_bukti)->toBe('surat.pdf');

    Storage::disk('public')->assertMissing('bukti_peminjaman/lama.png');
    Storage::disk('public')->assertExists($peminjaman->bukti);
});

it('menghapus data beserta berkasnya', function () {
    $user = User::factory()->create();
    $peminjaman = Peminjaman::factory()->create(['bukti' => 'bukti_peminjaman/hapus.png']);
    Storage::disk('public')->put('bukti_peminjaman/hapus.png', 'isi');

    $this->actingAs($user)->deleteJson("/peminjaman/{$peminjaman->id}")->assertOk();

    $this->assertDatabaseMissing('peminjamans', ['id' => $peminjaman->id]);
    Storage::disk('public')->assertMissing('bukti_peminjaman/hapus.png');
});

it('mengembalikan 404 untuk data yang tidak ada', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/peminjaman/9999/edit')->assertNotFound();
});

it('menolak request AJAX tanpa token CSRF', function () {
    // Pada environment testing Laravel mematikan pengecekan CSRF; di sini middleware
    // dibuat ulang tanpa melewati pengecekan agar perilaku produksi (419) ikut diuji.
    $this->app->instance(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, new class($this->app, $this->app['encrypter']) extends \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken
    {
        protected function runningUnitTests()
        {
            return false;
        }
    });

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withHeader('Accept', 'application/json')
        ->post('/peminjaman', dataPeminjaman())
        ->assertStatus(419);

    $this->assertDatabaseCount('peminjamans', 0);
});

