// Directory: resources/views/admin/peminjamans/partials/rows.blade.php
@foreach($peminjamans as $index => $peminjaman)
    @php
        $colors = ['primary', 'danger', 'info', 'success', 'warning', 'secondary'];
        $avatarColor = $colors[$index % count($colors)];
        $initial = strtoupper(substr($peminjaman->member?->name ?? 'U', 0, 1));
    @endphp
    <tr data-id="{{ $peminjaman->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-peminjaman" value="{{ $peminjaman->id }}">
        </td>
        <td>{{ $peminjaman->id }}</td>
        <td>{{ $peminjaman->member?->name ?? 'N/A' }}</td>
        <td>{{ $peminjaman->bukuItem?->buku?->judul ?? 'N/A' }} - {{ $peminjaman->bukuItem?->barcode ?? 'N/A' }}</td>
        <td>{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</td>
        <td>{{ $peminjaman->tanggal_kembali_rencana->format('d M Y') }}</td>
        <td><span class="badge bg-{{ $peminjaman->status == 'Dipinjam' ? 'info' : ($peminjaman->status == 'Dikembalikan' ? 'success' : 'danger') }}">{{ $peminjaman->status }}</span></td>
        <td>{{ number_format($peminjaman->pengembalian?->total_denda ?? 0) }}</td>
        <td>
            <div class="d-flex align-items-center">
                <span class="bg-{{ $avatarColor }} text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold me-2" style="width: 32px; height: 32px; font-size: 14px;">
                    {{ $initial }}
                </span>
                <span class="text-m365-blue">{{ $peminjaman->officer?->name ?? 'Unknown' }}</span>
            </div>
        </td>
        <td>{{ $peminjaman->updated_at->format('d M Y H:i') }}</td>
    </tr>
@endforeach

@if($peminjamans->isEmpty())
    <tr><td colspan="10" class="text-center py-3">No records found.</td></tr>
@endif
