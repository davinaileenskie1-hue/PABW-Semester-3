<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Peminjaman\Models\Peminjaman;

beforeEach(function () {
    $this->user = User::factory()->create();
});

/**
 * Valid loan payload.
 *
 * @return array<string, mixed>
 */
function peminjamanPayload(array $overrides = []): array
{
    return array_merge([
        'nama_peminjam' => 'Budi Santoso',
        'judul_buku' => 'Pemrograman Web dengan Laravel',
        'tanggal_pinjam' => '2026-11-01',
        'tanggal_kembali' => '2026-11-10',
        'status' => 'Dipinjam',
    ], $overrides);
}

test('guests are redirected to the login page', function () {
    $this->get('/peminjaman')->assertRedirect('/login');
    $this->getJson('/peminjaman/data')->assertUnauthorized();
});

test('authenticated users can open the loan page', function () {
    $this->actingAs($this->user)
        ->get('/peminjaman')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Peminjaman::index', shouldExist: false));
});

test('admins can also open the loan page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/peminjaman')
        ->assertOk();
});

test('the data endpoint returns every loan as json', function () {
    $peminjaman = Peminjaman::factory()->create(['nama_peminjam' => 'Budi Santoso']);

    $this->actingAs($this->user)
        ->getJson('/peminjaman/data')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $peminjaman->id,
            'nama_peminjam' => 'Budi Santoso',
            'bukti_url' => null,
        ]);
});

test('a loan can be stored together with its proof file', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', peminjamanPayload([
            'bukti' => UploadedFile::fake()->image('bukti.png'),
        ]))
        ->assertOk()
        ->assertJson(['success' => 'Data peminjaman berhasil disimpan.']);

    $peminjaman = Peminjaman::sole();

    expect($peminjaman->nama_peminjam)->toBe('Budi Santoso')
        ->and($peminjaman->nama_bukti)->toBe('bukti.png')
        ->and($peminjaman->bukti)->toStartWith('bukti_peminjaman/')
        ->and($peminjaman->bukti_url)->toContain('storage/bukti_peminjaman/');

    Storage::disk('public')->assertExists($peminjaman->bukti);
});

test('the proof file is required when storing', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', peminjamanPayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors('bukti')
        ->assertJsonPath('errors.bukti.0', 'Bukti peminjaman wajib diunggah.');

    expect(Peminjaman::count())->toBe(0);
});

test('storing validates the loan fields', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', [
            'nama_peminjam' => '',
            'judul_buku' => '',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_kembali' => 'bukan-tanggal',
            'status' => 'Hilang',
            'bukti' => UploadedFile::fake()->create('dokumen.txt', 10),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nama_peminjam', 'judul_buku', 'tanggal_kembali', 'status', 'bukti'])
        ->assertJsonPath('errors.status.0', 'Status harus Dipinjam atau Dikembalikan.')
        ->assertJsonPath('errors.bukti.0', 'Bukti harus berformat jpg, jpeg, png, atau pdf.');

    expect(Peminjaman::count())->toBe(0);
});

test('the return date may not be before the loan date', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', peminjamanPayload([
            'tanggal_pinjam' => '2026-11-10',
            'tanggal_kembali' => '2026-11-01',
            'bukti' => UploadedFile::fake()->image('bukti.png'),
        ]))
        ->assertStatus(422)
        ->assertJsonValidationErrors('tanggal_kembali')
        ->assertJsonPath('errors.tanggal_kembali.0', 'Tanggal kembali tidak boleh sebelum tanggal pinjam.');
});

test('a single loan can be fetched for the edit form', function () {
    $peminjaman = Peminjaman::factory()->create();

    $this->actingAs($this->user)
        ->getJson("/peminjaman/{$peminjaman->id}/edit")
        ->assertOk()
        ->assertJsonFragment([
            'id' => $peminjaman->id,
            'nama_peminjam' => $peminjaman->nama_peminjam,
        ]);
});

test('a loan can be updated without replacing its proof', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', peminjamanPayload([
            'bukti' => UploadedFile::fake()->image('bukti.png'),
        ]))
        ->assertOk();

    $peminjaman = Peminjaman::sole();
    $oldFile = $peminjaman->bukti;

    $this->actingAs($this->user)
        ->putJson("/peminjaman/{$peminjaman->id}", peminjamanPayload([
            'nama_peminjam' => 'Budi Diperbarui',
            'status' => 'Dikembalikan',
        ]))
        ->assertOk()
        ->assertJson(['success' => 'Data peminjaman berhasil diperbarui.']);

    $peminjaman->refresh();

    expect($peminjaman->nama_peminjam)->toBe('Budi Diperbarui')
        ->and($peminjaman->status)->toBe('Dikembalikan')
        ->and($peminjaman->bukti)->toBe($oldFile);

    Storage::disk('public')->assertExists($oldFile);
});

test('replacing the proof deletes the old file', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', peminjamanPayload([
            'bukti' => UploadedFile::fake()->image('lama.png'),
        ]))
        ->assertOk();

    $peminjaman = Peminjaman::sole();
    $oldFile = $peminjaman->bukti;

    $this->actingAs($this->user)
        ->putJson("/peminjaman/{$peminjaman->id}", peminjamanPayload([
            'bukti' => UploadedFile::fake()->image('baru.png'),
        ]))
        ->assertOk();

    $peminjaman->refresh();

    expect($peminjaman->bukti)->not->toBe($oldFile)
        ->and($peminjaman->nama_bukti)->toBe('baru.png');

    Storage::disk('public')->assertMissing($oldFile);
    Storage::disk('public')->assertExists($peminjaman->bukti);
});

test('a loan can be deleted together with its proof', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/peminjaman', peminjamanPayload([
            'bukti' => UploadedFile::fake()->image('bukti.png'),
        ]))
        ->assertOk();

    $peminjaman = Peminjaman::sole();
    $file = $peminjaman->bukti;

    $this->actingAs($this->user)
        ->deleteJson("/peminjaman/{$peminjaman->id}")
        ->assertOk()
        ->assertJson(['success' => 'Data peminjaman berhasil dihapus.']);

    expect(Peminjaman::count())->toBe(0);

    Storage::disk('public')->assertMissing($file);
});

test('a missing loan returns a 404', function () {
    $this->actingAs($this->user)
        ->getJson('/peminjaman/999/edit')
        ->assertNotFound();
});
