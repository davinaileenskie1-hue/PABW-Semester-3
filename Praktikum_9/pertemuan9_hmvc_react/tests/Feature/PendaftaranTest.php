<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Pendaftaran\Models\Daftar;

beforeEach(function () {
    $this->user = User::factory()->create();
});

/**
 * Valid registration payload.
 *
 * @return array<string, mixed>
 */
function daftarPayload(array $overrides = []): array
{
    return array_merge([
        'nama' => 'Budi Santoso',
        'asal_sekolah' => 'SMA Negeri 1 Bandung',
        'prodi_tujuan' => 'Informatika',
        'email' => 'budi@example.com',
        'no_hp' => '081234567890',
    ], $overrides);
}

test('guests are redirected to the login page', function () {
    $this->get('/pendaftaran')->assertRedirect('/login');
    $this->getJson('/pendaftaran/data')->assertUnauthorized();
});

test('authenticated users can open the registration page', function () {
    $this->actingAs($this->user)
        ->get('/pendaftaran')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Pendaftaran::index', shouldExist: false));
});

test('the data endpoint returns every registration as json', function () {
    $daftar = Daftar::factory()->create(['nama' => 'Budi Santoso']);

    $this->actingAs($this->user)
        ->getJson('/pendaftaran/data')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $daftar->id,
            'nama' => 'Budi Santoso',
            'berkas_url' => null,
        ]);
});

test('a registration can be stored together with its file', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload([
            'berkas' => UploadedFile::fake()->image('ijazah.png'),
        ]))
        ->assertOk()
        ->assertJson(['success' => 'Data berhasil disimpan.']);

    $daftar = Daftar::sole();

    expect($daftar->nama)->toBe('Budi Santoso')
        ->and($daftar->nama_berkas)->toBe('ijazah.png')
        ->and($daftar->berkas)->toStartWith('berkas_pendaftaran/')
        ->and($daftar->berkas_url)->toContain('storage/berkas_pendaftaran/');

    Storage::disk('public')->assertExists($daftar->berkas);
});

test('the file is required when storing', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors('berkas')
        ->assertJsonPath('errors.berkas.0', 'Berkas wajib diunggah.');

    expect(Daftar::count())->toBe(0);
});

test('storing validates the registration fields', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', [
            'nama' => '',
            'asal_sekolah' => '',
            'prodi_tujuan' => '',
            'email' => 'bukan-email',
            'no_hp' => 'abc',
            'berkas' => UploadedFile::fake()->create('dokumen.txt', 10),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nama', 'asal_sekolah', 'prodi_tujuan', 'email', 'no_hp', 'berkas'])
        ->assertJsonPath('errors.no_hp.0', 'No HP hanya boleh berisi angka (8-20 digit).')
        ->assertJsonPath('errors.berkas.0', 'Berkas harus berformat jpg, jpeg, png, atau pdf.');

    expect(Daftar::count())->toBe(0);
});

test('the email of a registration must be unique', function () {
    Storage::fake('public');

    Daftar::factory()->create(['email' => 'budi@example.com']);

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload([
            'berkas' => UploadedFile::fake()->image('ijazah.png'),
        ]))
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('a single registration can be fetched for the edit form', function () {
    $daftar = Daftar::factory()->create();

    $this->actingAs($this->user)
        ->getJson("/pendaftaran/{$daftar->id}/edit")
        ->assertOk()
        ->assertJsonFragment([
            'id' => $daftar->id,
            'email' => $daftar->email,
        ]);
});

test('a registration can be updated without replacing the file', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload([
            'berkas' => UploadedFile::fake()->image('ijazah.png'),
        ]))
        ->assertOk();

    $daftar = Daftar::sole();
    $oldFile = $daftar->berkas;

    $this->actingAs($this->user)
        ->putJson("/pendaftaran/{$daftar->id}", daftarPayload([
            'nama' => 'Budi Diperbarui',
            'prodi_tujuan' => 'Sistem Informasi',
        ]))
        ->assertOk()
        ->assertJson(['success' => 'Data berhasil diperbarui.']);

    $daftar->refresh();

    expect($daftar->nama)->toBe('Budi Diperbarui')
        ->and($daftar->prodi_tujuan)->toBe('Sistem Informasi')
        ->and($daftar->berkas)->toBe($oldFile);

    Storage::disk('public')->assertExists($oldFile);
});

test('replacing the file deletes the old one', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload([
            'berkas' => UploadedFile::fake()->image('lama.png'),
        ]))
        ->assertOk();

    $daftar = Daftar::sole();
    $oldFile = $daftar->berkas;

    $this->actingAs($this->user)
        ->putJson("/pendaftaran/{$daftar->id}", daftarPayload([
            'berkas' => UploadedFile::fake()->image('baru.png'),
        ]))
        ->assertOk();

    $daftar->refresh();

    expect($daftar->berkas)->not->toBe($oldFile)
        ->and($daftar->nama_berkas)->toBe('baru.png');

    Storage::disk('public')->assertMissing($oldFile);
    Storage::disk('public')->assertExists($daftar->berkas);
});

test('updating keeps its own email as valid', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload([
            'berkas' => UploadedFile::fake()->image('ijazah.png'),
        ]))
        ->assertOk();

    $daftar = Daftar::sole();

    $this->actingAs($this->user)
        ->putJson("/pendaftaran/{$daftar->id}", daftarPayload(['nama' => 'Budi Santoso']))
        ->assertOk()
        ->assertJsonMissingValidationErrors('email');
});

test('a registration can be deleted together with its file', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson('/pendaftaran', daftarPayload([
            'berkas' => UploadedFile::fake()->image('ijazah.png'),
        ]))
        ->assertOk();

    $daftar = Daftar::sole();
    $file = $daftar->berkas;

    $this->actingAs($this->user)
        ->deleteJson("/pendaftaran/{$daftar->id}")
        ->assertOk()
        ->assertJson(['success' => 'Data berhasil dihapus.']);

    expect(Daftar::count())->toBe(0);

    Storage::disk('public')->assertMissing($file);
});

test('a missing registration returns a 404', function () {
    $this->actingAs($this->user)
        ->getJson('/pendaftaran/999/edit')
        ->assertNotFound();
});
