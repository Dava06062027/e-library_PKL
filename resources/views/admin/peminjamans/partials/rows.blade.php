@foreach($peminjamans as $index => $peminjaman)
    @php
        $colors = ['primary', 'danger', 'info', 'success', 'warning', 'secondary'];
        $avatarColor = $colors[$index % count($colors)];
        $initial = strtoupper(substr($peminjaman->member?->name ?? 'U', 0, 1));


        $daysLate = 0;
        if ($peminjaman->status === 'Dipinjam') {
            $dueDate = \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana);
            $today = \Carbon\Carbon::today();
            if ($today->gt($dueDate)) {
                $daysLate = $today->diffInDays($dueDate);
            }
        } elseif ($peminjaman->pengembalian) {
            $dueDate = \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana);
            $returnDate = \Carbon\Carbon::parse($peminjaman->pengembalian->tanggal_kembali_aktual);
            if ($returnDate->gt($dueDate)) {
                $daysLate = $returnDate->diffInDays($dueDate);
            }
        }


        $perpanjanganCount = $peminjaman->perpanjangans()->count();


        $returnDateFormatted = '';
        $returnOfficerName = '';
        if ($peminjaman->pengembalian) {
            $returnDateFormatted = $peminjaman->pengembalian->tanggal_kembali_aktual
                ? \Carbon\Carbon::parse($peminjaman->pengembalian->tanggal_kembali_aktual)->format('d M Y')
                : '';
            $returnOfficerName = $peminjaman->pengembalian->officer?->name ?? 'N/A';
        }
    @endphp
    <tr data-id="{{ $peminjaman->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox"
                   class="form-check-input select-peminjaman"
                   value="{{ $peminjaman->id }}"
                   data-status="{{ $peminjaman->status }}"
                   data-member-name="{{ $peminjaman->member?->name ?? 'N/A' }}"
                   data-return-date="{{ $returnDateFormatted }}"
                   data-officer-name="{{ $returnOfficerName }}"
                   data-perpanjangan-count="{{ $perpanjanganCount }}">
        </td>
        <td>{{ $peminjaman->id }}</td>
        <td>{{ $peminjaman->member?->name ?? 'N/A' }}</td>
        <td>{{ $peminjaman->bukuItem?->buku?->judul ?? 'N/A' }}</td>
        <td>{{ $peminjaman->bukuItem?->barcode ?? 'N/A' }}</td>
        <td>{{ $peminjaman->tanggal_pinjam ? $peminjaman->tanggal_pinjam->format('d M Y') : '-' }}</td>
        <td>{{ $peminjaman->tanggal_kembali_rencana ? $peminjaman->tanggal_kembali_rencana->format('d M Y') : '-' }}</td>
        <td>
            <span class="badge bg-{{ $peminjaman->status == 'Dipinjam' ? 'info' : ($peminjaman->status == 'Dikembalikan' ? 'success' : 'danger') }}">
                {{ $peminjaman->status }}
            </span>
            @if($perpanjanganCount > 0)
                <span class="badge bg-warning text-dark ms-1" title="Sudah diperpanjang {{ $perpanjanganCount }}x">
                    <i class="bi bi-arrow-clockwise"></i> {{ $perpanjanganCount }}x
                </span>
            @endif
        </td>
        <td>
            @if($daysLate > 0)
                <span class="badge bg-danger">{{ $daysLate }} hari</span>
            @else
                <span class="badge bg-success">0 hari</span>
            @endif
        </td>
        <td>Rp {{ number_format($peminjaman->pengembalian?->total_denda ?? 0, 0, ',', '.') }}</td>
        <td>
            <div class="d-flex align-items-center">
                <span class="bg-{{ $avatarColor }} text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold me-2" style="width: 32px; height: 32px; font-size: 14px;">
                    {{ strtoupper(substr($peminjaman->officer?->name ?? 'U', 0, 1)) }}
                </span>
                <span class="text-m365-blue">{{ $peminjaman->officer?->name ?? 'Unknown' }}</span>
            </div>
        </td>
    </tr>
@endforeach

@if($peminjamans->isEmpty())
    <tr><td colspan="11" class="text-center py-3">No records found.</td></tr>
@endif
