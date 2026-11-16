@php
    $isEdit = $fasilitas->exists;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Periksa kembali isian Anda:</div>
        <ul class="mb-0 small ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $isEdit ? route('fasilitas.update', $fasilitas) : route('fasilitas.store') }}"
      method="POST" novalidate>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-7">
            <label for="nama" class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
            <input type="text" name="nama" id="nama" value="{{ old('nama', $fasilitas->nama) }}"
                   class="form-control @error('nama') is-invalid @enderror"
                   placeholder="Contoh: Laboratorium Komputer 1" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-5">
            <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $fasilitas->lokasi) }}"
                   class="form-control @error('lokasi') is-invalid @enderror"
                   placeholder="Contoh: Gedung C, Lantai 1" required>
            @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
            <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                <option value="">-- Pilih kategori --</option>
                @foreach (\Modules\TitikSampah\Models\TitikSampah::KATEGORI as $kategori)
                    <option value="{{ $kategori }}" @selected(old('kategori', $TitikSampah->kategori) === $kategori)>{{ $kategori }}</option>
                @endforeach
            </select>
            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
            <select name="kondisi" id="kondisi" class="form-select @error('kondisi') is-invalid @enderror" required>
                @foreach (\Modules\TitikSampah\Models\TitikSampah::KONDISI as $value => $label)
                    <option value="{{ $value }}" @selected(old('kondisi', $TitikSampah->kondisi ?? 'baik') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('kondisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label for="deskripsi" class="form-label">Deskripsi <span class="text-secondary small">(opsional)</span></label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                      class="form-control @error('deskripsi') is-invalid @enderror"
                      placeholder="Keterangan singkat mengenai fasilitas...">{{ old('deskripsi', $TitikSampah->deskripsi) }}</textarea>
            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Fasilitas' }}
        </button>
        <a href="{{ route('TitikSampah.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
