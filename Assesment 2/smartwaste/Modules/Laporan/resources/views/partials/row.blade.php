<tr id="pengaduan-row-{{ $item->id }}">
    <td class="text-secondary">#{{ $item->id }}</td>
    <td>
        <div class="fw-semibold">{{ $item->judul }}</div>
        <small class="text-secondary">{{ \Illuminate\Support\Str::limit($item->deskripsi, 80) }}</small>
    </td>
    <td>
        <div class="fw-semibold">{{ $item->fasilitas->nama ?? '-' }}</div>
        <small class="text-secondary">{{ $item->fasilitas->lokasi ?? '' }}</small>
    </td>
    <td>
        {{ $item->user->name ?? '-' }}
        <small class="d-block text-secondary">{{ $item->user->email ?? '' }}</small>
    </td>
    <td class="small">{{ $item->tanggal_pengaduan?->format('d M Y') ?? '-' }}</td>
    <td>
        @if ($item->foto_url)
            <a href="{{ $item->foto_url }}" target="_blank" rel="noopener">
                <img src="{{ $item->foto_url }}" alt="foto kerusakan" class="foto-thumb">
            </a>
        @else
            <span class="text-secondary small">-</span>
        @endif
    </td>
    <td>
        @if ($canManage)
            <select class="form-select form-select-sm status-select" style="width:130px;"
                    data-id="{{ $item->id }}" data-current="{{ $item->status }}">
                @foreach (\Modules\Pengaduan\Models\Pengaduan::STATUS as $value => $label)
                    <option value="{{ $value }}" @selected($item->status === $value)>{{ $label }}</option>
                @endforeach
            </select>
        @else
            {!! $item->status_badge !!}
        @endif
    </td>
    <td class="text-end text-nowrap">
        @if ($canManage)
            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-pengaduan"
                    data-id="{{ $item->id }}"
                    data-name="pengaduan #{{ $item->id }}"
                    title="Hapus">
                <i class="bi bi-trash"></i>
            </button>
        @else
            <span class="text-secondary small">-</span>
        @endif
    </td>
</tr>
