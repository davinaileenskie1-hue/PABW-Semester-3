@extends('layouts.app')

@section('title', 'Pengaduan Fasilitas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Pengaduan Fasilitas</h4>
            <p class="text-secondary mb-0 small">
                @if ($canManage)
                    Kelola laporan kerusakan &mdash; ubah status dan hapus tanpa refresh halaman (AJAX jQuery).
                @else
                    Daftar pengaduan yang Anda kirim. Gunakan tombol <strong>Buat Pengaduan</strong> untuk melapor.
                @endif
            </p>
        </div>
        @if (auth()->user()->isMahasiswa())
            <button class="btn btn-primary" id="btn-add-pengaduan">
                <i class="bi bi-plus-lg me-1"></i>Buat Pengaduan
            </button>
        @endif
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="text-secondary small">Total Pengaduan</div><div class="fs-4 fw-bold">{{ $rekap['total'] }}</div></div>
                    <div class="stat-icon text-bg-primary bg-opacity-10 text-primary"><i class="bi bi-megaphone"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="text-secondary small">Status Baru</div><div class="fs-4 fw-bold">{{ $rekap['baru'] }}</div></div>
                    <div class="stat-icon text-bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><div class="text-secondary small">Sedang Diproses</div><div class="fs-4 fw-bold">{{ $rekap['diproses'] }}</div></div>
                    <div class="stat-icon text-bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            {{-- Filter & pencarian (server-side) --}}
            <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-2 mb-3 no-loader">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ $keyword }}" class="form-control"
                               placeholder="Cari judul, fasilitas, atau nama pelapor...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua status</option>
                        @foreach (\Modules\Pengaduan\Models\Pengaduan::STATUS as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">Terapkan</button>
                    @if ($keyword !== '' || $status)
                        <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="pengaduan-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">ID</th>
                            <th>Pengaduan</th>
                            <th>Fasilitas</th>
                            <th>Pelapor</th>
                            <th>Tanggal</th>
                            <th>Foto</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pengaduan-body">
                        @forelse ($pengaduan as $item)
                            @include('pengaduan::partials.row', ['item' => $item, 'canManage' => $canManage])
                        @empty
                            <tr id="pengaduan-empty-row">
                                <td colspan="8" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data pengaduan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <small class="text-secondary">
                    Menampilkan {{ $pengaduan->firstItem() ?? 0 }}&ndash;{{ $pengaduan->lastItem() ?? 0 }}
                    dari {{ $pengaduan->total() }} pengaduan
                </small>
                {{ $pengaduan->links() }}
            </div>
        </div>
    </div>

    @if (auth()->user()->isMahasiswa())
        {{-- Modal form pengaduan (AJAX + FormData) --}}
        <div class="modal fade" id="pengaduan-modal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="pengaduan-form" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Pengaduan Kerusakan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label for="fasilitas_id" class="form-label">Fasilitas <span class="text-danger">*</span></label>
                                    <select name="fasilitas_id" id="fasilitas_id" class="form-select" required>
                                        <option value="">-- Pilih fasilitas --</option>
                                        @foreach ($fasilitas as $f)
                                            <option value="{{ $f->id }}">{{ $f->nama }} &mdash; {{ $f->lokasi }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-md-5">
                                    <label for="tanggal_pengaduan" class="form-label">Tanggal Pengaduan <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pengaduan" id="tanggal_pengaduan"
                                           class="form-control" max="{{ date('Y-m-d') }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12">
                                    <label for="judul" class="form-label">Judul Pengaduan <span class="text-danger">*</span></label>
                                    <input type="text" name="judul" id="judul" class="form-control"
                                           placeholder="Contoh: Proyektor ruang 204 sering mati" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12">
                                    <label for="deskripsi" class="form-label">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control"
                                              placeholder="Jelaskan kondisi kerusakan, kapan terjadi, dan dampaknya..." required></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12">
                                    <label for="foto" class="form-label">Foto Kerusakan <span class="text-secondary small">(jpg/png/webp, maks 2 MB)</span></label>
                                    <input type="file" name="foto" id="foto" accept="image/*" class="form-control">
                                    <div class="invalid-feedback"></div>
                                    <img src="" id="foto-preview" alt="preview" class="rounded border mt-2 d-none"
                                         style="width:140px;height:100px;object-fit:cover;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="pengaduan-submit">
                                <span class="spinner-border spinner-border-sm d-none me-1" id="pengaduan-spinner"></span>
                                <i class="bi bi-send me-1" id="pengaduan-save-icon"></i>Kirim Pengaduan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    $(function () {
        const storeUrl = "{{ route('pengaduan.store') }}";
        const updateUrl = "{{ route('pengaduan.update', '__ID__') }}";
        const destroyUrl = "{{ route('pengaduan.destroy', '__ID__') }}";
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const canManage = @json($canManage);

        // ---------- Buka modal tambah pengaduan ----------
        $('#btn-add-pengaduan').on('click', function () {
            const form = document.getElementById('pengaduan-form');
            form.reset();
            App.clearErrors('#pengaduan-form');
            $('#tanggal_pengaduan').val(new Date().toISOString().slice(0, 10));
            $('#foto-preview').addClass('d-none').attr('src', '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pengaduan-modal')).show();
        });

        // ---------- Preview foto ----------
        $('#foto').on('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => $('#foto-preview').attr('src', e.target.result).removeClass('d-none');
            reader.readAsDataURL(file);
        });

        // ---------- Submit pengaduan via AJAX (FormData + JSON) ----------
        $('#pengaduan-form').on('submit', function (event) {
            event.preventDefault();
            App.clearErrors('#pengaduan-form');

            const formData = new FormData(this);

            $.ajax({
                url: storeUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                beforeSend: function () {
                    $('#pengaduan-submit').prop('disabled', true);
                    $('#pengaduan-spinner').removeClass('d-none');
                    $('#pengaduan-save-icon').addClass('d-none');
                },
                success: function (response) {
                    const $row = $(response.row);
                    $('#pengaduan-empty-row').remove();
                    $('#pengaduan-body').prepend($row);
                    $row.addClass('fade-in');

                    App.toast(response.message, 'success');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('pengaduan-modal')).hide();
                    $('#pengaduan-form')[0].reset();
                    $('#foto-preview').addClass('d-none').attr('src', '');
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON) {
                        App.showErrors('#pengaduan-form', xhr.responseJSON.errors || {});
                        App.toast(xhr.responseJSON.message || 'Data belum valid, periksa kembali.', 'danger');
                    } else if (xhr.status === 403) {
                        App.toast('Anda tidak memiliki hak akses untuk aksi ini.', 'danger');
                    } else {
                        App.toast('Terjadi kesalahan pada server (' + xhr.status + ').', 'danger');
                    }
                },
                complete: function () {
                    $('#pengaduan-submit').prop('disabled', false);
                    $('#pengaduan-spinner').addClass('d-none');
                    $('#pengaduan-save-icon').removeClass('d-none');
                }
            });
        });

        // ---------- Ubah status via AJAX (khusus petugas) ----------
        $(document).on('change', '.status-select', function () {
            const $select = $(this);
            const id = $select.data('id');
            const previous = $select.data('current');

            $.ajax({
                url: updateUrl.replace('__ID__', id),
                type: 'POST',
                data: {
                    _token: csrfToken,
                    _method: 'PUT',
                    status: $select.val()
                },
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    const $row = $(response.row);
                    $('#pengaduan-row-' + id).replaceWith($row);
                    $row.addClass('fade-in');
                    App.toast(response.message, 'success');
                },
                error: function (xhr) {
                    $select.val(previous);
                    if (xhr.status === 422 && xhr.responseJSON) {
                        App.toast(xhr.responseJSON.message || 'Status tidak valid.', 'danger');
                    } else if (xhr.status === 403) {
                        App.toast('Anda tidak memiliki hak akses untuk mengubah status.', 'danger');
                    } else {
                        App.toast('Gagal mengubah status (' + xhr.status + ').', 'danger');
                    }
                }
            });
        });

        // ---------- Hapus via AJAX (khusus petugas) ----------
        $(document).on('click', '.btn-delete-pengaduan', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            App.confirmDelete('Yakin ingin menghapus ' + name + '?', function () {
                $.ajax({
                    url: destroyUrl.replace('__ID__', id),
                    type: 'POST',
                    data: { _token: csrfToken, _method: 'DELETE' },
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        $('#pengaduan-row-' + id).fadeOut(200, function () {
                            $(this).remove();
                            if ($('#pengaduan-body tr').length === 0) {
                                $('#pengaduan-body').html(
                                    '<tr id="pengaduan-empty-row"><td colspan="8" class="text-center text-secondary py-5">' +
                                    '<i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data pengaduan.</td></tr>'
                                );
                            }
                        });
                        App.toast(response.message, 'success');
                    },
                    error: function (xhr) {
                        App.toast(xhr.status === 403
                            ? 'Anda tidak memiliki hak akses untuk menghapus data ini.'
                            : 'Gagal menghapus data.', 'danger');
                    }
                });
            });
        });
    });
</script>
@endpush
