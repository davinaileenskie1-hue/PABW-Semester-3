@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Peminjaman Buku</h4>
            <p class="text-muted mb-0 small">Data tersimpan tanpa memuat ulang halaman.</p>
        </div>
        <button type="button" class="btn btn-primary" id="addBtn">
            <i class="bi bi-plus-lg me-1"></i> Catat Peminjaman
        </button>
    </div>

    <div id="alert-container"></div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
            <span class="fw-semibold">
                Daftar Peminjaman <span class="badge text-bg-light border ms-1" id="total-count">0</span>
            </span>
            <div class="input-group" style="max-width: 300px">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" id="search" class="form-control border-start-0" placeholder="Cari peminjam...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="peminjaman-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal form -->
    <div class="modal fade" id="peminjamanModal" tabindex="-1" aria-labelledby="peminjamanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="peminjamanModalLabel">Catat Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="peminjamanForm">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        @foreach ([
                            'nama_peminjam' => ['label' => 'Nama Peminjam', 'type' => 'text', 'placeholder' => 'Nama anggota perpustakaan'],
                            'judul_buku' => ['label' => 'Judul Buku', 'type' => 'text', 'placeholder' => 'Contoh: Clean Code'],
                        ] as $name => $field)
                            <div class="mb-3">
                                <label for="{{ $name }}" class="form-label">{{ $field['label'] }} <span class="text-danger">*</span></label>
                                <input type="{{ $field['type'] }}" class="form-control" id="{{ $name }}" name="{{ $name }}" placeholder="{{ $field['placeholder'] }}" required>
                                <div class="invalid-feedback" id="{{ $name }}-error"></div>
                            </div>
                        @endforeach

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" required>
                                <div class="invalid-feedback" id="tanggal_pinjam-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali">
                                <div class="invalid-feedback" id="tanggal_kembali-error"></div>
                                <div class="form-text">Kosongkan jika belum dikembalikan.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Dikembalikan">Dikembalikan</option>
                            </select>
                            <div class="invalid-feedback" id="status-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="bukti" class="form-label">
                                Bukti Peminjaman <span class="text-danger" id="bukti-required">*</span>
                            </label>
                            <div class="small mb-2 d-none" id="bukti-current">
                                Bukti saat ini: <a href="#" target="_blank" rel="noopener" id="bukti-current-link"></a>
                            </div>
                            <input type="file" class="form-control" id="bukti" name="bukti" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="invalid-feedback" id="bukti-error"></div>
                            <div class="form-text" id="bukti-help">Format jpg, jpeg, png, atau pdf. Maksimal 2 MB.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <span class="submit-text">Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const urls = {
            data: "{{ route('peminjaman.data') }}",
            store: "{{ route('peminjaman.store') }}",
            edit: "{{ route('peminjaman.edit', ':id') }}",
            update: "{{ route('peminjaman.update', ':id') }}",
            destroy: "{{ route('peminjaman.destroy', ':id') }}",
        };

        let records = [];

        // Set CSRF token untuk semua request AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // Escape karakter HTML (termasuk tanda kutip untuk atribut) agar data user tidak dieksekusi sebagai script (XSS)
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Format tanggal ISO (yyyy-mm-dd) menjadi dd/mm/yyyy
        function formatDate(value) {
            if (! value) {
                return '-';
            }

            const parts = String(value).substring(0, 10).split('-');

            return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
        }

        // Link untuk membuka bukti di tab baru
        function fileLink(item) {
            if (! item.bukti_url) {
                return '<span class="text-muted">-</span>';
            }

            const isPdf = (item.nama_bukti || item.bukti).toLowerCase().endsWith('.pdf');
            const icon = isPdf ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-primary';

            return `<a href="${escapeHtml(item.bukti_url)}" target="_blank" rel="noopener" class="small text-decoration-none">
                        <i class="bi ${icon} me-1"></i>${escapeHtml(item.nama_bukti || 'Lihat bukti')}
                    </a>`;
        }

        // Mode input berkas: wajib saat tambah, opsional saat edit
        function setFileMode(item = null) {
            $('#bukti').val('');
            $('#bukti-required').toggleClass('d-none', item !== null);
            $('#bukti-current').toggleClass('d-none', ! item?.bukti_url);
            $('#bukti-help').text(item
                ? 'Kosongkan jika tidak ingin mengganti bukti. Format jpg, jpeg, png, atau pdf. Maksimal 2 MB.'
                : 'Format jpg, jpeg, png, atau pdf. Maksimal 2 MB.');

            if (item?.bukti_url) {
                $('#bukti-current-link').attr('href', item.bukti_url).text(item.nama_bukti || 'Lihat bukti');
            }
        }

        function showAlert(message, type = 'success') {
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            const alert = $(`
                <div class="alert alert-${type} alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi ${icon}"></i>
                    <span>${escapeHtml(message)}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            `);

            $('#alert-container').html(alert);
            setTimeout(() => bootstrap.Alert.getOrCreateInstance(alert[0]).close(), 4000);
        }

        function clearErrors() {
            $('#peminjamanForm .invalid-feedback').text('');
            $('#peminjamanForm .is-invalid').removeClass('is-invalid');
        }

        function emptyRow(icon, text) {
            return `
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="bi ${icon}"></i>
                            <p class="mt-2 mb-0">${text}</p>
                        </div>
                    </td>
                </tr>`;
        }

        // Tampilkan data ke tabel sesuai kata kunci pencarian
        function renderTable() {
            const keyword = $('#search').val().trim().toLowerCase();
            const rows = records.filter((item) =>
                [item.nama_peminjam, item.judul_buku, item.status, item.tanggal_pinjam, item.tanggal_kembali]
                    .some((value) => (value || '').toLowerCase().includes(keyword))
            );

            $('#total-count').text(records.length);

            if (records.length === 0) {
                $('#peminjaman-table tbody').html(emptyRow('bi-inbox', 'Belum ada data peminjaman. Klik <strong>Catat Peminjaman</strong> untuk memulai.'));
                return;
            }

            if (rows.length === 0) {
                $('#peminjaman-table tbody').html(emptyRow('bi-search', 'Tidak ada peminjaman yang cocok dengan pencarian.'));
                return;
            }

            const html = rows.map((item, index) => `
                <tr>
                    <td class="text-muted">${index + 1}</td>
                    <td class="fw-medium">${escapeHtml(item.nama_peminjam)}</td>
                    <td>${escapeHtml(item.judul_buku)}</td>
                    <td class="small">${formatDate(item.tanggal_pinjam)}</td>
                    <td class="small">${formatDate(item.tanggal_kembali)}</td>
                    <td><span class="badge ${item.status === 'Dipinjam' ? 'text-bg-warning' : 'text-bg-success'}">${escapeHtml(item.status)}</span></td>
                    <td>${fileLink(item)}</td>
                    <td class="text-end text-nowrap">
                        <button data-id="${item.id}" class="edit btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button data-id="${item.id}" data-nama="${escapeHtml(item.nama_peminjam)}" class="delete btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            `).join('');

            $('#peminjaman-table tbody').html(html);
        }

        // Muat data dari server
        function loadData() {
            $.ajax({
                url: urls.data,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    records = data;
                    renderTable();
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    $('#peminjaman-table tbody').html(emptyRow('bi-wifi-off', 'Gagal memuat data. Silakan refresh halaman.'));
                }
            });
        }

        loadData();
        $('#search').on('input', renderTable);

        const peminjamanModal = new bootstrap.Modal(document.getElementById('peminjamanModal'));

        $('#peminjamanModal').on('shown.bs.modal', function () {
            $('#nama_peminjam').trigger('focus');
        });

        $('#peminjamanModal').on('hidden.bs.modal', function () {
            $('#peminjamanForm').trigger('reset');
            $('#id').val('');
            clearErrors();
            setFileMode();
        });

        // Tambah data
        $('#addBtn').click(function () {
            $('#peminjamanModalLabel').text('Catat Peminjaman');
            $('#tanggal_pinjam').val(new Date().toISOString().substring(0, 10));
            setFileMode();
            peminjamanModal.show();
        });

        // Tekan Enter di form = simpan
        $('#peminjamanForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').trigger('click');
        });

        // Simpan data (create / update)
        $('#saveBtn').click(function () {
            const btn = $(this);
            const id = $('#id').val();

            btn.prop('disabled', true);
            btn.find('.submit-text').text('Menyimpan...');
            btn.find('.spinner-border').removeClass('d-none');
            clearErrors();

            // FormData dipakai (bukan serialize) agar file ikut terkirim
            const formData = new FormData(document.getElementById('peminjamanForm'));
            if (id) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: id ? urls.update.replace(':id', id) : urls.store,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    peminjamanModal.hide();
                    loadData();
                    showAlert(response.success);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}-error`).text(value[0]);
                        });
                        $('#peminjamanForm .is-invalid:first').focus();
                    } else {
                        console.error('Error:', xhr.responseText);
                        peminjamanModal.hide();
                        showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
                    }
                },
                complete: function () {
                    btn.prop('disabled', false);
                    btn.find('.submit-text').text('Simpan');
                    btn.find('.spinner-border').addClass('d-none');
                }
            });
        });

        // Edit data
        $(document).on('click', '.edit', function () {
            const id = $(this).data('id');

            $.get(urls.edit.replace(':id', id), function (data) {
                $('#peminjamanModalLabel').text('Edit Peminjaman');
                $('#id').val(data.id);
                $('#nama_peminjam').val(data.nama_peminjam);
                $('#judul_buku').val(data.judul_buku);
                $('#tanggal_pinjam').val((data.tanggal_pinjam || '').substring(0, 10));
                $('#tanggal_kembali').val((data.tanggal_kembali || '').substring(0, 10));
                $('#status').val(data.status);
                setFileMode(data);
                peminjamanModal.show();
            }).fail(function () {
                showAlert('Gagal memuat data untuk diedit.', 'danger');
            });
        });

        // Hapus data
        $(document).on('click', '.delete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            confirmDelete(`Data peminjaman "${nama}" akan dihapus dan tidak bisa dikembalikan.`).then(function (confirmed) {
                if (! confirmed) {
                    return;
                }

                $.ajax({
                    type: 'DELETE',
                    url: urls.destroy.replace(':id', id),
                    dataType: 'json',
                    success: function (response) {
                        loadData();
                        showAlert(response.success);
                    },
                    error: function () {
                        showAlert('Gagal menghapus data. Silakan coba lagi.', 'danger');
                    }
                });
            });
        });
    });
</script>
@endpush
