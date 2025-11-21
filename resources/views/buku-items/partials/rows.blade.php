@foreach($items as $item)
    @php
        $kondisiColor = $item->kondisi === 'Baik' ? 'success' : ($item->kondisi === 'Rusak' ? 'warning' : 'danger');
        $statusColor = $item->status === 'Tersedia' ? 'primary' : ($item->status === 'Dipinjam' ? 'info' : 'secondary');
    @endphp
    <tr data-id="{{ $item->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-item" value="{{ $item->id }}">
        </td>
        <td class="text-m365-blue">{{ $item->barcode }}</td>
        <td><span class="badge bg-{{ $kondisiColor }}">{{ $item->kondisi }}</span></td>
        <td><span class="badge bg-{{ $statusColor }}">{{ $item->status }}</span></td>
        <td>{{ $item->sumber }}</td>
        <td>{{ $item->buku?->judul ?? '-' }}</td>
        <td>{{ $item->rak?->nama ?? '-' }}</td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $item->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
        </td>
    </tr>
@endforeach

@if($items->isEmpty())
    <tr class="no-data">
        <td colspan="8" class="text-center py-5 text-secondary">
            <i class="bi bi-bookshelf" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">No items found.</p>
        </td>
    </tr>
@endif
