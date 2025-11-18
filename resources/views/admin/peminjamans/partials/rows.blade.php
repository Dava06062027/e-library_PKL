@foreach($peminjamans as $peminjaman)
    @php
        // ✅ Normalize status - handle empty/null as 'Dipinjam'
        $displayStatus = $peminjaman->status_transaksi;

        if (empty($displayStatus) || is_null($displayStatus)) {
            $displayStatus = 'Dipinjam';
            $badgeClass = 'bg-warning text-dark';
        } else {
            $badgeClass = $peminjaman->getStatusBadgeClass();
        }
    @endphp

    <tr data-id="{{ $peminjaman->id }}"
        data-status="{{ $displayStatus }}"
        data-total-items="{{ $peminjaman->total_items }}"
        data-items-returned="{{ $peminjaman->items_dikembalikan }}"
        class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-peminjaman" value="{{ $peminjaman->id }}">
        </td>
        <td>
            <span class="badge bg-primary">{{ $peminjaman->transaction_number }}</span>
        </td>
        <td class="fw-semibold">
            {{ $peminjaman->member->name ?? 'N/A' }}
            <br><small class="text-muted">{{ $peminjaman->member->email ?? '' }}</small>
        </td>
        <td class="text-secondary small">
            {{ $peminjaman->tanggal_pinjam->format('d M Y') }}
        </td>
        <td class="text-secondary small">
            {{ $peminjaman->tanggal_kembali_rencana->format('d M Y') }}
            @if($peminjaman->isOverdue() && in_array($displayStatus, ['Dipinjam', 'Diperpanjang']))
                <br><small class="text-danger fw-bold">Telat {{ $peminjaman->days_late }} hari</small>
            @endif
        </td>
        <td>
            <span class="badge bg-secondary">{{ $peminjaman->items_dikembalikan }}/{{ $peminjaman->total_items }} kembali</span>
            <br><small class="text-muted">
                {{ $peminjaman->items->pluck('bukuItem.barcode')->implode(', ') }}
            </small>
        </td>
        <td>
            <span class="badge {{ $badgeClass }}">
                {{ $displayStatus }}
            </span>
            @if($peminjaman->total_denda > 0)
                <br><small class="text-danger">Denda: Rp {{ number_format($peminjaman->total_denda, 0, ',', '.') }}</small>
            @endif
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail"
                    data-id="{{ $peminjaman->id }}"
                    title="Lihat Detail">
                <i class="bi bi-eye"></i> Detail
            </button>
        </td>
    </tr>
@endforeach

@if($peminjamans->isEmpty())
    <tr>
        <td colspan="8" class="text-center py-5 text-secondary">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">Tidak ada peminjaman ditemukan.</p>
        </td>
    </tr>
@endif
